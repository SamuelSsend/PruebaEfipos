<?php

namespace Doctrine\DBAL\Schema;

use Doctrine\DBAL\DBALException;
use Doctrine\DBAL\Driver\Exception;
use Doctrine\DBAL\Platforms\SQLServerPlatform;
use Doctrine\DBAL\Types\Type;
use Throwable;

use function assert;
use function count;
use function in_array;
use function is_string;
use function preg_match;
use function sprintf;
use function str_replace;
use function strpos;
use function strtok;

/**
 * SQL Server Schema Manager.
 */
class SQLServerSchemaManager extends AbstractSchemaManager
{
    /**
     * {@inheritdoc}
     */
    public function dropDatabase($database)
    {
        try {
            parent::dropDatabase($database);
        } catch (DBALException $exception) {
            $exception = $exception->getPrevious();
            assert($exception instanceof Throwable);

            if (! $exception instanceof Exception) {
                throw $exception;
            }

            // If we have a error code 3702, the drop database operation failed
            // because of active connections on the database.
            // To force dropping the database, we first have to close all active connections
            // on that database and issue the drop database operation again.
            if ($exception->getErrorCode() !== 3702) {
                throw $exception;
            }

            $this->closeActiveDatabaseConnections($database);

            parent::dropDatabase($database);
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function _getPortableSequenceDefinition($sequence)
    {
        return new Sequence($sequence['name'], (int) $sequence['increment'], (int) $sequence['start_value']);
    }

    /**
     * {@inheritdoc}
     */
    protected function _getPortableTableColumnDefinition($tableColumn)
    {
        $dbType = strtok($tableColumn['type'], '(), ');
        assert(is_string($dbType));

        $fixed   = null;
        $length  = (int) $tableColumn['length'];
        $default = $tableColumn['default'];

        if (! isset($tableColumn['name'])) {
            $tableColumn['name'] = '';
        }

        if ($default !== null) {
            $default = $this->parseDefaultExpression($default);
        }

        switch ($dbType) {
        case 'nchar':
        case 'nvarchar':
        case 'ntext':
            // Unicode data requires 2 bytes per character
            $length /= 2;
            break;

        case 'varchar':
            // TEXT type is returned as VARCHAR(MAX) with a length of -1
            if ($length === -1) {
                $dbType = 'text';
            }

            break;
        }

        if ($dbType === 'char' || $dbType === 'nchar' || $dbType === 'binary') {
            $fixed = true;
        }

        $type                   = $this->_platform->getDoctrineTypeMapping($dbType);
        $type                   = $this->extractDoctrineTypeFromComment($tableColumn['comment'], $type);
        $tableColumn['comment'] = $this->removeDoctrineTypeFromComment($tableColumn['comment'], $type);

        $options = [
            'length'        => $length === 0 || ! in_array($type, ['text', 'string']) ? null : $length,
            'unsigned'      => false,
            'fixed'         => (bool) $fixed,
            'default'       => $default,
            'notnull'       => (bool) $tableColumn['notnull'],
            'scale'         => $tableColumn['scale'],
            'precision'     => $tableColumn['precision'],
            'autoincrement' => (bool) $tableColumn['autoincrement'],
            'comment'       => $tableColumn['comment'] !== '' ? $tableColumn['comment'] : null,
        ];

        $column = new Column($tableColumn['name'], Type::getType($type), $options);

        if (isset($tableColumn['collation']) && $tableColumn['collation'] !== 'NULL') {
            $column->setPlatformOption('collation', $tableColumn['collation']);
        }

        return $column;
    }

    private function parseDefaultExpression(string $value): ?string
    {
        while (preg_match('/^\((.*)\)$/s', $value, $matches)) {
            $value = $matches[1];
        }

        if ($value === 'NULL') {
            return null;
        }

        if (preg_match('/^\'(.*)\'$/s', $value, $matches)) {
            $value = str_replace("''", "'", $matches[1]);
        }

        if ($value === 'getdate()') {
            return $this->_platform->getCurrentTimestampSQL();
        }

        return $value;
    }

    /**
     * {@inheritdoc}
     */
    protected function _getPortableTableForeignKeysList($tableForeignKeys)
    {
        $foreignKeys = [];

        foreach ($tableForeignKeys as $tableForeignKey) {
            $name = $tableForeignKey['ForeignKey'];

            if (! isset($foreignKeys[$name])) {
                $foreignKeys[$name] = [
                    'local_columns' => [$tableForeignKey['ColumnName']],
                    'foreign_table' => $tableForeignKey['ReferenceTableName'],
                    'foreign_columns' => [$tableForeignKey['ReferenceColumnName']],
                    'name' => $name,
                    'options' => [
                        'onUpdate' => str_replace('_', ' ', $tableForeignKey['update_referential_action_desc']),
                        'onDelete' => str_replace('_', ' ', $tableForeignKey['delete_referential_action_desc']),
                    ],
                ];
            } else {
                $foreignKeys[$name]['local_columns'][]   = $tableForeignKey['ColumnName'];
                $foreignKeys[$name]['foreign_columns'][] = $tableForeignKey['ReferenceColumnName'];
            }
        }

        return parent::_getPortableTableForeignKeysList($foreignKeys);
    }

    /**
     * {@inheritdoc}
     */
    protected function _getPortableTableIndexesList($tableIndexes, $tableName = null)
    {
        foreach ($tableIndexes as &$tableIndex) {
            $tableIndex['non_unique'] = (bool) $tableIndex['non_unique'];
            $tableIndex['primary']    = (bool) $tableIndex['primary'];
            $tableIndex['flags']      = $tableIndex['flags'] ? [$tableIndex['flags']] : null;
        }

        return parent::_getPortableTableIndexesList($tableIndexes, $tableName);
    }

    /**
     * {@inheritdoc}
     */
    protected function _getPortableTableForeignKeyDefinition($tableForeignKey)
    {
        return new ForeignKeyConstraint(
            $tableForeignKey['local_columns'],
            $tableForeignKey['foreign_table'],
            $tableForeignKey['foreign_columns'],
            $tableForeignKey['name'],
            $tableForeignKey['options']
        );
    }

    /**
     * {@inheritdoc}
     */
    protected function _getPortableTableDefinition($table)
    {
        if (isset($table['schema_name']) && $table['schema_name'] !== 'dbo') {
            return $table['schema_name'] . '.' . $table['name'];
        }

        return $table['name'];
    }

    /**
     * {@inheritdoc}
     */
    protected function _getPortableDatabaseDefinition($database)
    {
        return $database['name'];
    }

    /**
     * {@inheritdoc}
     */
    protected function getPortableNamespaceDefinition(array $namespace)
    {
        return $namespace['name'];
    }

    /**
     * {@inheritdoc}
     */
    protected function _getPortableViewDefinition($view)
    {
        // @todo
        return new View($view['name'], '');
    }

    /**
     * {@inheritdoc}
     */
    public function listTableIndexes($table)
    {
        $sql = $this->_platform->getListTableIndexesSQL($table, $this->_conn->getDatabase());

        try {
            $tableIndexes = $this->_conn->fetchAllAssociative($sql);
        } catch (DBALException $e) {
            if (strpos($e->getMessage(), 'SQLSTATE [01000, 15472]') === 0) {
                return [];
            }

            throw $e;
        }

        return $this->_getPortableTableIndexesList($tableIndexes, $table);
    }

    /**
     * {@inheritdoc}
     */
    public function alterTable(TableDiff $tableDiff)
    {
        if (count($tableDiff->removedColumns) > 0) {
            foreach ($tableDiff->removedColumns as $col) {
                $columnConstraintSql = $this->getColumnConstraintSQL($tableDiff->name, $col->getName());
                foreach ($this->_conn->fetchAllAssociative($columnConstraintSql) as $constraint) {
                    $this->_conn->exec(
                        sprintf(
                            'ALTER TABLE %s DROP CONSTRAINT %s',
                            $tableDiff->name,
                            $constraint['Name']
                        )
                    );
                }
            }
        }

        parent::alterTable($tableDiff);
    }

    /**
     * Returns the SQL to retrieve the constraints for a given column.
     *
     * @param string $table
     * @param string $column
     *
     * @return string
     */
    private function getColumnConstraintSQL($table, $column)
    {
        return "SELECT sysobjects.[Name]
            FROM sysobjects INNER JOIN (SELECT [Name],[ID] FROM sysobjects WHERE XType = 'U') AS Tab
            ON Tab.[ID] = sysobjects.[Parent_Obj]
            INNER JOIN sys.default_constraints DefCons ON DefCons.[object_id] = sysobjects.[ID]
            INNER JOIN syscolumns Col ON Col.[ColID] = DefCons.[parent_column_id] AND Col.[ID] = Tab.[ID]
            WHERE Col.[Name] = " . $this->_conn->quote($column) . ' AND Tab.[Name] = ' . $this->_conn->quote($table) . '
            ORDER BY Col.[Name]';
    }

    /**
     * Closes currently active connections on the given database.
     *
     * This is useful to force DROP DATABASE operations which could fail because of active connections.
     *
     * @param string $database The name of the database to close currently active connections for.
     *
     * @return void
     */
    private function closeActiveDatabaseConnections($database)
    {
        $database = new Identifier($database);

        $this->_execSql(
            sprintf(
                'ALTER DATABASE %s SET SINGLE_USER WITH ROLLBACK IMMEDIATE',
                $database->getQuotedName($this->_platform)
            )
        );
    }

    /**
     * @param string $name
     */
    public function listTableDetails($name): Table
    {
        $table = parent::listTableDetails($name);

        $platform = $this->_platform;
        assert($platform instanceof SQLServerPlatform);
        $sql = $platform->getListTableMetadataSQL($name);

        $tableOptions = $this->_conn->fetchAssociative($sql);

        if ($tableOptions !== false) {
            $table->addOption('comment', $tableOptions['table_comment']);
        }

        return $table;
    }
}

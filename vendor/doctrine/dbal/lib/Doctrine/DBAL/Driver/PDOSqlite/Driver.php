<?php

namespace Doctrine\DBAL\Driver\PDOSqlite;

use Doctrine\DBAL\Driver\AbstractSQLiteDriver;
use Doctrine\DBAL\Driver\PDO;
use Doctrine\DBAL\Exception;
use Doctrine\DBAL\Platforms\SqlitePlatform;
use Doctrine\Deprecations\Deprecation;
use PDOException;

use function array_merge;

/**
 * The PDO Sqlite driver.
 *
 * @deprecated Use {@link PDO\SQLite\Driver} instead.
 */
class Driver extends AbstractSQLiteDriver
{
    /**
     * @var mixed[] 
     */
    protected $_userDefinedFunctions = [
        'sqrt' => ['callback' => [SqlitePlatform::class, 'udfSqrt'], 'numArgs' => 1],
        'mod'  => ['callback' => [SqlitePlatform::class, 'udfMod'], 'numArgs' => 2],
        'locate'  => ['callback' => [SqlitePlatform::class, 'udfLocate'], 'numArgs' => -1],
    ];

    /**
     * {@inheritdoc}
     */
    public function connect(array $params, $username = null, $password = null, array $driverOptions = [])
    {
        if (isset($driverOptions['userDefinedFunctions'])) {
            $this->_userDefinedFunctions = array_merge(
                $this->_userDefinedFunctions,
                $driverOptions['userDefinedFunctions']
            );
            unset($driverOptions['userDefinedFunctions']);
        }

        try {
            $pdo = new PDO\Connection(
                $this->_constructPdoDsn($params),
                $username,
                $password,
                $driverOptions
            );
        } catch (PDOException $ex) {
            throw Exception::driverException($this, $ex);
        }

        foreach ($this->_userDefinedFunctions as $fn => $data) {
            $pdo->sqliteCreateFunction($fn, $data['callback'], $data['numArgs']);
        }

        return $pdo;
    }

    /**
     * Constructs the Sqlite PDO DSN.
     *
     * @param mixed[] $params
     *
     * @return string The DSN.
     */
    protected function _constructPdoDsn(array $params)
    {
        $dsn = 'sqlite:';
        if (isset($params['path'])) {
            $dsn .= $params['path'];
        } elseif (isset($params['memory'])) {
            $dsn .= ':memory:';
        }

        return $dsn;
    }

    /**
     * {@inheritdoc}
     *
     * @deprecated
     */
    public function getName()
    {
        Deprecation::trigger(
            'doctrine/dbal',
            'https://github.com/doctrine/dbal/issues/3580',
            'Driver::getName() is deprecated'
        );

        return 'pdo_sqlite';
    }
}

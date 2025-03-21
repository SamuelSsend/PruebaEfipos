<?php

namespace Doctrine\DBAL\Driver\IBMDB2;

use Doctrine\DBAL\Driver\FetchUtils;
use Doctrine\DBAL\Driver\IBMDB2\Exception\CannotCopyStreamToStream;
use Doctrine\DBAL\Driver\IBMDB2\Exception\CannotCreateTemporaryFile;
use Doctrine\DBAL\Driver\IBMDB2\Exception\CannotWriteToTemporaryFile;
use Doctrine\DBAL\Driver\IBMDB2\Exception\StatementError;
use Doctrine\DBAL\Driver\Result;
use Doctrine\DBAL\Driver\Statement as StatementInterface;
use Doctrine\DBAL\Driver\StatementIterator;
use Doctrine\DBAL\FetchMode;
use Doctrine\DBAL\ParameterType;
use IteratorAggregate;
use PDO;
use ReflectionClass;
use ReflectionObject;
use ReflectionProperty;
use ReturnTypeWillChange;
use stdClass;

use function array_change_key_case;
use function assert;
use function db2_bind_param;
use function db2_execute;
use function db2_fetch_array;
use function db2_fetch_assoc;
use function db2_fetch_both;
use function db2_fetch_object;
use function db2_free_result;
use function db2_num_fields;
use function db2_num_rows;
use function db2_stmt_error;
use function db2_stmt_errormsg;
use function error_get_last;
use function fclose;
use function func_get_args;
use function func_num_args;
use function fwrite;
use function gettype;
use function is_int;
use function is_object;
use function is_resource;
use function is_string;
use function ksort;
use function sprintf;
use function stream_copy_to_stream;
use function stream_get_meta_data;
use function strtolower;
use function tmpfile;

use const CASE_LOWER;
use const DB2_BINARY;
use const DB2_CHAR;
use const DB2_LONG;
use const DB2_PARAM_FILE;
use const DB2_PARAM_IN;

/**
 * @deprecated Use {@link Statement} instead
 */
class DB2Statement implements IteratorAggregate, StatementInterface, Result
{
    /**
     * @var resource 
     */
    private $stmt;

    /**
     * @var mixed[] 
     */
    private $bindParam = [];

    /**
     * Map of LOB parameter positions to the tuples containing reference to the variable bound to the driver statement
     * and the temporary file handle bound to the underlying statement
     *
     * @var mixed[][]
     */
    private $lobs = [];

    /**
     * @var string Name of the default class to instantiate when fetching class instances. 
     */
    private $defaultFetchClass = '\stdClass';

    /**
     * @var mixed[] Constructor arguments for the default class to instantiate when fetching class instances. 
     */
    private $defaultFetchClassCtorArgs = [];

    /**
     * @var int 
     */
    private $defaultFetchMode = FetchMode::MIXED;

    /**
     * Indicates whether the statement is in the state when fetching results is possible
     *
     * @var bool
     */
    private $result = false;

    /**
     * @internal The statement can be only instantiated by its driver connection.
     *
     * @param resource $stmt
     */
    public function __construct($stmt)
    {
        $this->stmt = $stmt;
    }

    /**
     * {@inheritdoc}
     */
    public function bindValue($param, $value, $type = ParameterType::STRING)
    {
        assert(is_int($param));

        return $this->bindParam($param, $value, $type);
    }

    /**
     * {@inheritdoc}
     */
    public function bindParam($param, &$variable, $type = ParameterType::STRING, $length = null)
    {
        assert(is_int($param));

        switch ($type) {
        case ParameterType::INTEGER:
            $this->bind($param, $variable, DB2_PARAM_IN, DB2_LONG);
            break;

        case ParameterType::LARGE_OBJECT:
            if (isset($this->lobs[$param])) {
                [, $handle] = $this->lobs[$param];
                fclose($handle);
            }

            $handle = $this->createTemporaryFile();
            $path   = stream_get_meta_data($handle)['uri'];

            $this->bind($param, $path, DB2_PARAM_FILE, DB2_BINARY);

            $this->lobs[$param] = [&$variable, $handle];
            break;

        default:
            $this->bind($param, $variable, DB2_PARAM_IN, DB2_CHAR);
            break;
        }

        return true;
    }

    /**
     * @param int   $position Parameter position
     * @param mixed $variable
     *
     * @throws DB2Exception
     */
    private function bind($position, &$variable, int $parameterType, int $dataType): void
    {
        $this->bindParam[$position] =& $variable;

        if (! db2_bind_param($this->stmt, $position, 'variable', $parameterType, $dataType)) {
            throw StatementError::new($this->stmt);
        }
    }

    /**
     * {@inheritdoc}
     *
     * @deprecated Use free() instead.
     */
    public function closeCursor()
    {
        $this->bindParam = [];

        if (! db2_free_result($this->stmt)) {
            return false;
        }

        $this->result = false;

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function columnCount()
    {
        return db2_num_fields($this->stmt) ?: 0;
    }

    /**
     * {@inheritdoc}
     *
     * @deprecated The error information is available via exceptions.
     */
    public function errorCode()
    {
        return db2_stmt_error();
    }

    /**
     * {@inheritdoc}
     *
     * @deprecated The error information is available via exceptions.
     */
    public function errorInfo()
    {
        return [
            db2_stmt_errormsg(),
            db2_stmt_error(),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function execute($params = null)
    {
        if ($params === null) {
            ksort($this->bindParam);

            $params = [];

            foreach ($this->bindParam as $column => $value) {
                $params[] = $value;
            }
        }

        foreach ($this->lobs as [$source, $target]) {
            if (is_resource($source)) {
                $this->copyStreamToStream($source, $target);

                continue;
            }

            $this->writeStringToStream($source, $target);
        }

        $retval = db2_execute($this->stmt, $params);

        foreach ($this->lobs as [, $handle]) {
            fclose($handle);
        }

        $this->lobs = [];

        if ($retval === false) {
            throw StatementError::new($this->stmt);
        }

        $this->result = true;

        return $retval;
    }

    /**
     * {@inheritdoc}
     *
     * @deprecated Use one of the fetch- or iterate-related methods.
     */
    public function setFetchMode($fetchMode, $arg2 = null, $arg3 = null)
    {
        $this->defaultFetchMode          = $fetchMode;
        $this->defaultFetchClass         = $arg2 ?: $this->defaultFetchClass;
        $this->defaultFetchClassCtorArgs = $arg3 ? (array) $arg3 : $this->defaultFetchClassCtorArgs;

        return true;
    }

    /**
     * {@inheritdoc}
     *
     * @deprecated Use iterateNumeric(), iterateAssociative() or iterateColumn() instead.
     */
    #[ReturnTypeWillChange]
    public function getIterator()
    {
        return new StatementIterator($this);
    }

    /**
     * {@inheritdoc}
     *
     * @deprecated Use fetchNumeric(), fetchAssociative() or fetchOne() instead.
     */
    public function fetch($fetchMode = null, $cursorOrientation = PDO::FETCH_ORI_NEXT, $cursorOffset = 0)
    {
        // do not try fetching from the statement if it's not expected to contain result
        // in order to prevent exceptional situation
        if (! $this->result) {
            return false;
        }

        $fetchMode = $fetchMode ?: $this->defaultFetchMode;
        switch ($fetchMode) {
        case FetchMode::COLUMN:
            return $this->fetchColumn();

        case FetchMode::MIXED:
            return db2_fetch_both($this->stmt);

        case FetchMode::ASSOCIATIVE:
            return db2_fetch_assoc($this->stmt);

        case FetchMode::CUSTOM_OBJECT:
            $className = $this->defaultFetchClass;
            $ctorArgs  = $this->defaultFetchClassCtorArgs;

            if (func_num_args() >= 2) {
                $args      = func_get_args();
                $className = $args[1];
                $ctorArgs  = $args[2] ?? [];
            }

            $result = db2_fetch_object($this->stmt);

            if ($result instanceof stdClass) {
                $result = $this->castObject($result, $className, $ctorArgs);
            }

            return $result;

        case FetchMode::NUMERIC:
            return db2_fetch_array($this->stmt);

        case FetchMode::STANDARD_OBJECT:
            return db2_fetch_object($this->stmt);

        default:
            throw new DB2Exception('Given Fetch-Style ' . $fetchMode . ' is not supported.');
        }
    }

    /**
     * {@inheritdoc}
     *
     * @deprecated Use fetchAllNumeric(), fetchAllAssociative() or fetchFirstColumn() instead.
     */
    public function fetchAll($fetchMode = null, $fetchArgument = null, $ctorArgs = null)
    {
        $rows = [];

        switch ($fetchMode) {
        case FetchMode::CUSTOM_OBJECT:
            while (($row = $this->fetch(...func_get_args())) !== false) {
                $rows[] = $row;
            }

            break;

        case FetchMode::COLUMN:
            while (($row = $this->fetchColumn()) !== false) {
                $rows[] = $row;
            }

            break;

        default:
            while (($row = $this->fetch($fetchMode)) !== false) {
                $rows[] = $row;
            }
        }

        return $rows;
    }

    /**
     * {@inheritdoc}
     *
     * @deprecated Use fetchOne() instead.
     */
    public function fetchColumn($columnIndex = 0)
    {
        $row = $this->fetch(FetchMode::NUMERIC);

        if ($row === false) {
            return false;
        }

        return $row[$columnIndex] ?? null;
    }

    /**
     * {@inheritDoc}
     */
    public function fetchNumeric()
    {
        if (! $this->result) {
            return false;
        }

        return db2_fetch_array($this->stmt);
    }

    /**
     * {@inheritdoc}
     */
    public function fetchAssociative()
    {
        // do not try fetching from the statement if it's not expected to contain the result
        // in order to prevent exceptional situation
        if (! $this->result) {
            return false;
        }

        return db2_fetch_assoc($this->stmt);
    }

    /**
     * {@inheritdoc}
     */
    public function fetchOne()
    {
        return FetchUtils::fetchOne($this);
    }

    /**
     * {@inheritdoc}
     */
    public function fetchAllNumeric(): array
    {
        return FetchUtils::fetchAllNumeric($this);
    }

    /**
     * {@inheritdoc}
     */
    public function fetchAllAssociative(): array
    {
        return FetchUtils::fetchAllAssociative($this);
    }

    /**
     * {@inheritdoc}
     */
    public function fetchFirstColumn(): array
    {
        return FetchUtils::fetchFirstColumn($this);
    }

    /**
     * {@inheritdoc}
     */
    public function rowCount()
    {
        return @db2_num_rows($this->stmt) ? : 0;
    }

    public function free(): void
    {
        $this->bindParam = [];

        db2_free_result($this->stmt);

        $this->result = false;
    }

    /**
     * Casts a stdClass object to the given class name mapping its' properties.
     *
     * @param stdClass            $sourceObject     Object to cast from.
     * @param class-string|object $destinationClass Name of the class or class instance to cast to.
     * @param mixed[]             $ctorArgs         Arguments to use for constructing the destination class instance.
     *
     * @return object
     *
     * @throws DB2Exception
     */
    private function castObject(stdClass $sourceObject, $destinationClass, array $ctorArgs = [])
    {
        if (! is_string($destinationClass)) {
            if (! is_object($destinationClass)) {
                throw new DB2Exception(
                    sprintf(
                        'Destination class has to be of type string or object, %s given.',
                        gettype($destinationClass)
                    )
                );
            }
        } else {
            $destinationClass = new ReflectionClass($destinationClass);
            $destinationClass = $destinationClass->newInstanceArgs($ctorArgs);
        }

        $sourceReflection           = new ReflectionObject($sourceObject);
        $destinationClassReflection = new ReflectionObject($destinationClass);
        /**
 * @var ReflectionProperty[] $destinationProperties 
*/
        $destinationProperties = array_change_key_case($destinationClassReflection->getProperties(), CASE_LOWER);

        foreach ($sourceReflection->getProperties() as $sourceProperty) {
            $sourceProperty->setAccessible(true);

            $name  = $sourceProperty->getName();
            $value = $sourceProperty->getValue($sourceObject);

            // Try to find a case-matching property.
            if ($destinationClassReflection->hasProperty($name)) {
                $destinationProperty = $destinationClassReflection->getProperty($name);

                $destinationProperty->setAccessible(true);
                $destinationProperty->setValue($destinationClass, $value);

                continue;
            }

            $name = strtolower($name);

            // Try to find a property without matching case.
            // Fallback for the driver returning either all uppercase or all lowercase column names.
            if (isset($destinationProperties[$name])) {
                $destinationProperty = $destinationProperties[$name];

                $destinationProperty->setAccessible(true);
                $destinationProperty->setValue($destinationClass, $value);

                continue;
            }

            $destinationClass->$name = $value;
        }

        return $destinationClass;
    }

    /**
     * @return resource
     *
     * @throws DB2Exception
     */
    private function createTemporaryFile()
    {
        $handle = @tmpfile();

        if ($handle === false) {
            throw CannotCreateTemporaryFile::new(error_get_last());
        }

        return $handle;
    }

    /**
     * @param resource $source
     * @param resource $target
     *
     * @throws DB2Exception
     */
    private function copyStreamToStream($source, $target): void
    {
        if (@stream_copy_to_stream($source, $target) === false) {
            throw CannotCopyStreamToStream::new(error_get_last());
        }
    }

    /**
     * @param resource $target
     *
     * @throws DB2Exception
     */
    private function writeStringToStream(string $string, $target): void
    {
        if (@fwrite($target, $string) === false) {
            throw CannotWriteToTemporaryFile::new(error_get_last());
        }
    }
}

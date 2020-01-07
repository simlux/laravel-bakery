<?php declare(strict_types=1);

namespace Simlux\LaravelBakery\Model\DataTypes;

use Simlux\LaravelBakery\Console\Commands\AbstractCommand;

/**
 * Class AbstractDataType
 *
 * @package Simlux\LaravelBakery\Model\DataType
 */
abstract class AbstractDataType
{
    const DATATYPE_STRING  = 'string';
    const DATATYPE_INTEGER = 'int';
    const DATATYPE_DATE    = 'Carbon';
    const DATATYPE_FLOAT   = 'float';
    const DATATYPE_BOOLEAN = 'bool';

    const STEP_AUTOINCREMENT = 'autoincrement';
    const STEP_UNSIGNED      = 'unsigned';
    const STEP_NULLABLE      = 'nullable';
    const STEP_DEFAULT       = 'default';

    /**
     * @var array
     */
    public static $dataTypes = [
        self::DATATYPE_STRING  => StringDataType::class,
        self::DATATYPE_INTEGER => IntegerDataType::class,
        self::DATATYPE_DATE    => DateDataType::class,
        self::DATATYPE_FLOAT   => FloatDataType::class,
        self::DATATYPE_BOOLEAN => BooleanDataType::class,
    ];

    /**
     * @var array
     */
    public static $types = [];

    /**
     * @var int
     */
    protected $defaultType;

    /**
     * @var bool
     */
    public $type;

    /**
     * @var bool
     */
    public $nullable = false;

    /**
     * @var int
     */
    public $default;

    /**
     * @param string     $type
     * @param array|null $info
     *
     * @return AbstractDataType
     */
    public static function factory(string $type, array $info = null): AbstractDataType
    {
        $class = self::$dataTypes[$type];
        /** @var AbstractDataType $instance */
        $instance       = new $class();
        $instance->type = $type;

        if (!is_null($info)) {
            $instance->processInfo($info);
        }

        return $instance;
    }

    /**
     * @return array
     */
    public static function getDataTypes(): array
    {
        return array_keys(self::$dataTypes);
    }

    /**
     * @param bool $asString
     *
     * @return int|string
     */
    public function getDefaultType(bool $asString = false)
    {
        return $asString ? self::$types[$this->defaultType] : $this->defaultType;
    }

    /**
     * @param AbstractCommand $command
     * @param array           $skip
     *
     * @return void
     */
    abstract public function interact(AbstractCommand $command, array $skip = []): void;

    /**
     * @return string
     */
    abstract public function getPhpType(): string;

    /**
     * @return string
     */
    abstract public function getMethodName(): string;

    /**
     * @param string $name
     *
     * @return string
     */
    abstract public function getMethodParams(string $name): string;

    /**
     * @param int    $i
     * @param string $name
     *
     * @return array ['#', 'Name', 'Type', 'Length', 'Unsigned', 'Nullable', 'Default']
     */
    abstract public function getInfoForTable(int $i, string $name): array;

    /**
     * @param mixed $param
     *
     * @return string
     */
    public function paramToString($param): string
    {
        if (is_string($param)) {
            return sprintf("'%s'", $param);
        }

        if (is_bool($param)) {
            return $param ? 'true' : 'false';
        }

        return $param;
    }

    /**
     * @param string $name
     *
     * @return string
     */
    public function getConstString(string $name): string
    {
        return sprintf("const %s = '%s'", strtoupper($name), strtoupper($name));
    }

    /**
     * @param array $info
     *
     * @return void
     */
    public function processInfo(array $info): void
    {
        $this->type     = $info['datatype'];
        $this->nullable = $info['nullable'] === 1;

        if (!empty($info['default'])) {
            $this->default = $info['default'];
        }
    }

    /**
     * @param string $step
     * @param array  $skip
     *
     * @return bool
     */
    protected function skip(string $step, array $skip): bool
    {
        return in_array($step, $skip);
    }
}
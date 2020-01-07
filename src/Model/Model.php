<?php declare(strict_types=1);

namespace Simlux\LaravelBakery\Model;

use Simlux\LaravelBakery\Model\DataTypes\AbstractDataType;
use Str;

/**
 * Class Model
 *
 * @package Simlux\LaravelBakery\Model
 */
class Model
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $table;

    /**
     * @var ModelProperty[]
     */
    private $properties = [];

    /**
     * @var bool
     */
    private $useCarbon = false;

    /**
     * @var string
     */
    private $namespace = 'App';

    /**
     * @var string
     */
    private $modelNamespace = 'Models';

    /**
     * @var array|Index[]
     */
    private $indexes = [];

    /**
     * @var string
     */
    private $extends = \Illuminate\Database\Eloquent\Model::class;

    /**
     * Model constructor.
     *
     * @param string      $name
     * @param string|null $table
     */
    public function __construct(string $name, string $table = null)
    {
        $this->name  = $name;
        $this->table = is_null($table)
            ? self::model2table($name)
            : $table;
    }

    /**
     * @param string $modelName
     *
     * @return string
     */
    public static function model2table(string $modelName): string
    {
        return strtolower(
            Str::snake(
                Str::plural($modelName)
            )
        );
    }

    /**
     * @param string $table
     *
     * @return string
     */
    public static function table2model(string $table): string
    {
        return ucfirst(
            Str::camel(
                Str::singular($table)
            )
        );
    }

    /**
     * @return string
     */
    public function getTable(): string
    {
        return $this->table;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $table
     */
    public function setTable(string $table): void
    {
        $this->table = $table;
    }

    /**
     * @param ModelProperty $property
     */
    public function addProperty(ModelProperty $property): void
    {
        $this->properties[] = $property;

        if ($property->dataType->getPhpType() === 'Carbon') {
            $this->useCarbon = true;
        }
    }

    /**
     * @return bool
     */
    public function useCarbon(): bool
    {
        return $this->useCarbon;
    }

    /**
     * @return string
     */
    public function getNamespace(): string
    {
        return implode('\\', [$this->namespace, $this->modelNamespace]);
    }

    /**
     * @param bool $withNamespace
     *
     * @return string
     */
    public function getExtends(bool $withNamespace = true): string
    {
        if ($withNamespace) {
            return $this->extends;
        }

        return last(explode('\\', $this->extends));
    }

    /**
     * @param string $extends
     */
    public function setExtends(string $extends): void
    {
        $this->extends = $extends;
    }

    /**
     * @return string
     */
    public function getDateColumns(): string
    {
        return collect($this->properties)->filter(function (ModelProperty $property) {
            return $property->dataType->getPhpType() === AbstractDataType::DATATYPE_DATE;
        })->map(function (ModelProperty $property) {
            return sprintf("\t\t'%s',", $property->name);
        })->implode(PHP_EOL);
    }

    /**
     * @return string
     */
    public function getCasts(): string
    {
        return collect($this->properties)
            ->map(function (ModelProperty $property) {
                return $property->getCastString();
            })
            ->filter(function ($castString) {
                return !empty($castString);
            })
            ->implode(PHP_EOL);
    }

    /**
     * @return string
     */
    public function getPropertiesAsString(): string
    {
        return ' * ' . PHP_EOL . collect($this->properties)
                ->map(function (ModelProperty $property) {
                    return $property->getPropertyString();
                })
                ->implode(PHP_EOL);
    }

    /**
     * @return ModelProperty[]
     */
    public function getProperties(): array
    {
        return $this->properties;
    }

    /**
     * @param Index $index
     */
    public function addIndex(Index $index): void
    {
        $this->indexes[] = $index;
    }

    /**
     * @return array|Index[]
     */
    public function getIndexes()
    {
        return $this->indexes;
    }

    /**
     * @param bool $withNamespace
     *
     * @return string
     */
    public function getClassName(bool $withNamespace = true): string
    {
        if ($withNamespace) {
            return sprintf('%s\\%s', $this->getNamespace(), $this->name);
        }

        return $this->name;
    }

}

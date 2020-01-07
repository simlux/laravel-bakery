<?php declare(strict_types=1);

namespace Simlux\LaravelBakery\Model;

use Simlux\LaravelBakery\Model\DataTypes\AbstractDataType;
use Str;

/**
 * Class ModelSuggestion
 *
 * @package Simlux\LaravelBakery\Model
 */
class ModelSuggestion
{
    const AUTO_INCREMENT = 'autoincrement';
    const DEFAULT        = 'default';
    const DATATYPE       = 'datatype';

    /**
     * @var Model
     */
    private $model;

    /**
     * @var ModelProperty
     */
    private $property;

    /**
     * @var bool
     */
    private $first;

    /**
     * ModelSuggestion constructor.
     *
     * @param ModelProperty $property
     * @param bool          $first
     */
    public function __construct(ModelProperty $property, bool $first = false)
    {
        $this->property = $property;
        $this->first    = $first;
    }

    /**
     * @param ModelProperty $property
     */
    public function setProperty(ModelProperty $property): void
    {
        $this->property = $property;
    }

    /**
     * @param bool $first
     *
     * @return void
     */
    public function setFirst(bool $first): void
    {
        $this->first = $first;
    }

    /**
     * @param ModelProperty $property
     *
     * @return string
     */
    public function suggestType(ModelProperty $property): string
    {
        $type = AbstractDataType::DATATYPE_STRING;

        if ($this->first) {
            $type = AbstractDataType::DATATYPE_INTEGER;
        } else if (Str::endsWith($property->name, '_id')) {
            $type = AbstractDataType::DATATYPE_INTEGER;
        } else if (Str::endsWith($property->name, '_at')) {
            $type = AbstractDataType::DATATYPE_DATE;
        }

        return $type;
    }

}
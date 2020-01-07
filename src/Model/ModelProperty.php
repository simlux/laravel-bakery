<?php declare(strict_types=1);

namespace Simlux\LaravelBakery\Model;

use Simlux\LaravelBakery\Model\DataTypes\AbstractDataType;

/**
 * Class ModelProperty
 *
 * @package Simlux\LaravelBakery\Model
 */
class ModelProperty
{
    /**
     * @var string
     */
    public $name;

    /**
     * @var string
     */
    public $type;

    /**
     * @var AbstractDataType
     */
    public $dataType;

    /**
     * @var ForeignKey
     */
    public $foreignKey;

    /**
     * @return string|null
     */
    public function getCastString()
    {
        if ($this->dataType && !in_array($this->dataType->getPhpType(), [AbstractDataType::DATATYPE_STRING, AbstractDataType::DATATYPE_DATE])) {
            return sprintf("\t\t'%s' => '%s',", $this->name, $this->dataType->getPhpType());
        }
    }

    /**
     * @param string $prefix
     *
     * @return string
     */
    public function getPropertyString(string $prefix = ' * '): string
    {
        return sprintf('%s@property %s $%s', $prefix, $this->dataType->getPhpType(), $this->name);
    }

    /**
     * @return string
     */
    public function getMigrationString(): string
    {
        $string = sprintf(
            "\$table->%s(%s)%s",
            $this->dataType->getMethodName(),
            $this->dataType->getMethodParams($this->name),
            ''
        );

        if ($this->dataType->nullable) {
            $string .= '->nullable()';
        }

        if ($this->dataType->default) {
            $string .= sprintf('->default(%s)', $this->dataType->paramToString($this->dataType->default));
        }

        $string .= ';';

        return $string;
    }
}
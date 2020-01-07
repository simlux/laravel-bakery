<?php declare(strict_types=1);

namespace Simlux\LaravelBakery\Model;

use DB;
use Simlux\LaravelBakery\Console\Commands\AbstractCommand;
use Simlux\LaravelBakery\Model\DataTypes\AbstractDataType;
use Str;

/**
 * Class ModelPropertyInteraction
 *
 * @package Simlux\LaravelBakery\Model
 */
class ModelPropertyInteraction extends AbstractInteraction
{
    /**
     * @var bool
     */
    private $first;

    /**
     * @var array
     */
    private $skip;

    /**
     * @var InformationSchema
     */
    private $informationSchema;

    /**
     * ModelPropertyInteraction constructor.
     *
     * @param AbstractCommand $command
     * @param bool            $first
     * @param array           $skip
     */
    public function __construct(AbstractCommand $command, bool $first = false, array $skip = [])
    {
        $this->command = $command;
        $this->first   = $first;
        $this->skip    = $skip;

        $this->informationSchema = new InformationSchema();
    }

    /**
     * @param array $skip
     *
     * @return ModelProperty
     */
    public function getProperty(array $skip = []): ModelProperty
    {
        $property       = new ModelProperty();
        $property->name = $this->command->askRequired(
            'Name',
            $this->first ? 'id' : null,
            'A column needs a name!'
        );

        if ($this->isForeignKey($property->name)) {
            return $this->handleForeignKey($property);
        }

        $suggestion         = new ModelSuggestion($property, $this->first);
        $type               = $this->command->choice(
            'Base data type',
            AbstractDataType::getDataTypes(),
            $suggestion->suggestType($property)
        );
        $property->dataType = AbstractDataType::factory($type);
        $property->dataType->interact($this->command, $skip);

        return $property;
    }

    /**
     * @param string $name
     *
     * @return bool
     */
    private function isForeignKey(string $name): bool
    {
        $question = sprintf('Is column "%s" a foreign key?', $name);

        return Str::endsWith($name, '_id')
            && $this->command->confirm($question, true);
    }

    /**
     * @param ModelProperty $property
     *
     * @return ModelProperty
     */
    protected function handleForeignKey(ModelProperty $property): ModelProperty
    {
        $answer               = $this->command->choice('Choose foreign column', $this->informationSchema->getPrimaryKeys());
        $property->foreignKey = new ForeignKey();
        list($property->foreignKey->table, $property->foreignKey->column) = explode('.', $answer);

        $info               = $this->informationSchema->getColumnSpecs($property->foreignKey->table, $property->foreignKey->column);
        $property->dataType = AbstractDataType::factory(AbstractDataType::DATATYPE_INTEGER, $info);

        return $property;
    }
}
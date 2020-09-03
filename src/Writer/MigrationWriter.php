<?php declare(strict_types=1);

namespace Simlux\LaravelBakery\Writer;

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Collection;
use Simlux\LaravelBakery\Migration\MigrationHelper;
use Simlux\LaravelBakery\Model\ForeignKey;
use Simlux\LaravelBakery\Model\Index;
use Simlux\LaravelBakery\Model\ModelProperty;
use Str;

/**
 * Class MigrationWriter
 *
 * @package Simlux\LaravelBakery\Writer
 */
class MigrationWriter extends AbstractWriter
{
    /**
     * @var string
     */
    protected $template = 'migration';

    /**
     * @var bool
     */
    protected $declare = false;

    /**
     * @var string
     */
    protected $parentClass = Migration::class;

    /**
     * @return void
     */
    protected function beforeWrite(): void
    {
        parent::beforeWrite();

        $this->addClassVars();
        $this->addColumns();

        $foreignKeys = collect($this->model->getProperties())->filter(function (ModelProperty $property) {
            return $property->foreignKey instanceof ForeignKey;
        });

        $this->addForeignKeys($foreignKeys);
        $this->addIndexes($foreignKeys);
    }

    private function addClassVars(): void
    {
        $this->setVar('model', $this->model);
        $this->setVar('table', $this->model->getTable());
        $this->setVar('class', Str::plural($this->model->getName()));
        $this->useClass(MigrationHelper::class);

        if ($this->parentClass) {
            $this->useClass($this->parentClass);
        }
        $this->setVar('extends', $this->getParentClass());
    }

    private function addColumns(): void
    {
        $this->setVar('columns', collect($this->model->getProperties())
            ->map(function (ModelProperty $property) {
                return $property->getMigrationString($this->model);
            })
            ->implode(self::EOL . str_repeat(self::TAB, 3)));
    }

    /**
     * @param Collection $foreignKeys
     */
    private function addForeignKeys(Collection $foreignKeys): void
    {
        $this->setVar('foreignKeys', $foreignKeys->map(function (ModelProperty $property) {
            return $property->foreignKey->getForeignKeyMigrationString($property->name);
        })->implode(self::EOL . str_repeat(self::TAB, 3)));
    }

    /**
     * @param Collection $foreignKeys
     */
    private function addIndexes(Collection $foreignKeys): void
    {
        $indexes = array_merge(
            $foreignKeys->map(function (ModelProperty $property) {
                return $property->foreignKey->getIndexMigrationString($property->name);
            })->toArray(),
            collect($this->model->getIndexes())->map(function (Index $index) {
                return $index->getIndexMigrationString();
            })->toArray()
        );
        $this->setVar('indexes', implode(PHP_EOL . str_repeat(self::TAB, 3), $indexes));
    }

}
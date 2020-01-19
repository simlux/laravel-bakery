<?php declare(strict_types=1);

namespace Simlux\LaravelBakery\Writer;

use File;
use Illuminate\Database\Seeder;
use Simlux\LaravelBakery\Model\ModelProperty;
use Simlux\LaravelBakery\Seeder\CSVSeeder;
use Str;

/**
 * Class SeederWriter
 *
 * @package Simlux\LaravelBakery\Writer
 */
class SeederWriter extends AbstractWriter
{
    /**
     * @var string
     */
    protected $template = 'seeder';

    /**
     * @var bool
     */
    protected $declare = false;

    /**
     * @var string
     */
    protected $parentClass = Seeder::class;

    /**
     * @var bool
     */
    private $isCSVSeeder = false;

    /**
     * @return void
     */
    protected function beforeWrite(): void
    {
        parent::beforeWrite();

        $this->setVar('model', $this->model->getName());
        $this->setVar('class', Str::plural($this->model->getName()));

        if ($this->parentClass) {
            $this->useClass($this->parentClass);
        }
        $this->setVar('extends', $this->getParentClass());

        $this->setVar('properties', collect($this->model->getProperties())->map(function (ModelProperty $property) {
            return sprintf("'%s' => null,", $property->name);
        })->implode(self::EOL . str_repeat(self::TAB, 3)));
    }

    /**
     * @return void
     */
    protected function afterWrite(): void
    {
        parent::afterWrite();

        if ($this->isCSVSeeder) {
            $this->writeCSV();
        }
    }

    public function setCSVSeeder(): void
    {
        $this->isCSVSeeder = true;
        $this->parentClass = CSVSeeder::class;
    }

    public function writeCSV(): void
    {
        $csvPath = 'database/seeds/csv';
        $file    = base_path(sprintf('%s/%s.csv', $csvPath, $this->model->getTable()));

        if (!File::isDirectory($csvPath)) {
            File::makeDirectory($csvPath);
        }

        $content = collect($this->model->getProperties())
            ->filter(function (ModelProperty $property) {
                return $property->name !== 'id';
            })
            ->map(function (ModelProperty $property) {
                return $property->name;
            })->implode(',');

        File::put($file, $content . PHP_EOL);

        // @TODO print file
    }

}
<?php declare(strict_types=1);

namespace Simlux\LaravelBakery\Writer;

use Illuminate\Database\Seeder;
use Simlux\LaravelBakery\Model\ModelProperty;
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

}
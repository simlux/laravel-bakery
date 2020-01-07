<?php declare(strict_types=1);

namespace Simlux\LaravelBakery\Writer;

use Simlux\LaravelBakery\Model\ModelProperty;

/**
 * Class FactoryWriter
 *
 * @package Simlux\LaravelBakery\Writer
 */
class FactoryWriter extends AbstractWriter
{
    /**
     * @var string
     */
    protected $template = 'factory';

    /**
     * @var bool
     */
    protected $declare = false;

    /**
     * @var string
     */
    protected $parentClass;

    /**
     * @return void
     */
    protected function beforeWrite(): void
    {
        parent::beforeWrite();

        $this->setVar('model', $this->model->getName());

        $this->setVar('properties', collect($this->model->getProperties())->map(function (ModelProperty $property) {
            return sprintf("'%s' => null,", $property->name);
        })->implode(self::EOL . str_repeat(self::TAB, 2)));
    }

}
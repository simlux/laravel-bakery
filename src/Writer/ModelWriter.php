<?php declare(strict_types=1);

namespace Simlux\LaravelBakery\Writer;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class ModelWriter
 *
 * @package Simlux\LaravelBakery\Writer
 */
class ModelWriter extends AbstractWriter
{
    /**
     * @var string
     */
    protected $template = 'model';

    /**
     * @var bool
     */
    protected $declare = true;

    /**
     * @var string
     */
    protected $parentClass = Model::class;

    /**
     * @return void
     */
    protected function beforeWrite(): void
    {
        parent::beforeWrite();

        $this->setNamespace($this->model->getNamespace());
        $this->setParentClass($this->model->getExtends());

        if ($this->model->useCarbon()) {
            $this->useClass(Carbon::class);
        }

        $this->setVar('model', $this->model);
    }
}
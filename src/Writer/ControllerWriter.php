<?php declare(strict_types=1);

namespace Simlux\LaravelBakery\Writer;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

/**
 * Class ControllerWriter
 *
 * @package Simlux\LaravelBakery\Writer
 */
class ControllerWriter extends AbstractWriter
{
    /**
     * @var string
     */
    protected $template = 'controller';

    /**
     * @var bool
     */
    protected $declare = true;

    /**
     * @var string
     */
    protected $parentClass = Controller::class;

    /**
     * @return void
     * @throws \Throwable
     */
    protected function beforeWrite(): void
    {
        parent::beforeWrite();

        $this->setVar('namespace', $this->namespace);
        $this->setVar('model', $this->model->getName());
        $this->useClass(View::class);
        $this->useClass($this->model->getClassName(true));

        if ($this->parentClass) {
            $this->useClass($this->parentClass);
        }
        $this->setVar('extends', $this->getParentClass());

        $methods   = [];
        $methods[] = $this->createOverview();

        $this->setVar('methods', implode(PHP_EOL . "\t", $methods));
    }

    /**
     * @return string
     * @throws \Throwable
     */
    private function createOverview(): string
    {
        return view('laravel-bakery::overview_controller_method')
            ->with('model', $this->model)
            ->with('table', $this->model->getTable())
            ->render();
    }

}
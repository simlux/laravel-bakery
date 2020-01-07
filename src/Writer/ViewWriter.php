<?php declare(strict_types=1);

namespace Simlux\LaravelBakery\Writer;

use Config;
use File;
use Illuminate\View\View;
use Simlux\LaravelBakery\Model\InformationSchema;
use Simlux\LaravelBakery\Model\Model;

/**
 * Class ViewWriter
 *
 * @package Simlux\LaravelBakery\Writer
 */
class ViewWriter
{
    /**
     * @var string
     */
    private $model;

    /**
     * @var array
     */
    private $columns;

    /**
     * ViewWriter constructor.
     *
     * @param string $model
     * @param array  $columns
     */
    public function __construct(string $model, array $columns)
    {
        $this->model   = $model;
        $this->columns = $columns;
    }

    /**
     * @param string $file
     *
     * @return string|false
     * @throws \Throwable
     */
    public function writeOverview(string $file)
    {
        $view = view('laravel-bakery::overview')
            ->with('model', $this->model)
            ->with('columns', $this->columns)
            ->with('columnCells', $this->getColumnCells())
            ->with('edit_route', sprintf("{{ route('%s.detail', ['id' => \$result->id]) }}", $this->model));

        $this->setConfigVars($view);

        $content = $view->render();

        if (File::put($file, $content)) {
            return $file;
        }

        return false;
    }

    /**
     * @param string $file
     *
     * @return string|false
     * @throws \Throwable
     */
    public function writeDetail(string $file)
    {
        $informationSchema = new InformationSchema();

        $view = view('laravel-bakery::detail')
            ->with('model', $this->model)
            ->with('columns', $informationSchema->getColumns(Model::model2table($this->model)));

        $this->setConfigVars($view);

        $content = $view->render();

        if (File::put($file, $content)) {
            return $file;
        }

        return false;
    }

    private function setConfigVars(View $view): void
    {
        $view->with('containerClass', Config::get('laravel-bakery.view.content.container_class'))
            ->with('tableClass', implode(' ', Config::get('laravel-bakery.view.table.table_classes')))
            ->with('titleTag', Config::get('laravel-bakery.view.title.tag'));
    }

    private function getColumnCells(): string
    {
        return collect($this->columns)->map(function (string $column) {
            return sprintf('<td>{{ $result->%s }}</td>', $column);
        })->implode(PHP_EOL . str_repeat("\t", 5));
    }

}
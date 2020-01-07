<?php declare(strict_types=1);

namespace Simlux\LaravelBakery\Console\Commands;

use Config;
use File;
use Simlux\LaravelBakery\Model\InformationSchema;
use Simlux\LaravelBakery\Model\Model;
use Simlux\LaravelBakery\Writer\ControllerWriter;
use Simlux\LaravelBakery\Writer\ViewWriter;
use Str;

/**
 * Class ViewCommand
 *
 * @package Simlux\LaravelBakery\Console\Commands
 */
class ViewCommand extends AbstractCommand
{
    /**
     * @var string
     */
    protected $signature = 'bake:view';

    /**
     * @var string
     */
    protected $description;

    /**
     * @var InformationSchema
     */
    private $informationSchema;

    /**
     * @var string
     */
    private $table;

    /**
     * @var string
     */
    private $namespace = 'App\Http\Controllers';

    /**
     * @var string
     */
    private $modelName;

    /**
     * @return void
     * @throws \Throwable
     */
    public function handle(): void
    {
        $this->informationSchema = new InformationSchema();

        $this->table     = $this->chooseModel();
        $this->modelName = Model::table2model($this->table);

        $this->writeView();
        $this->writeController();
        $this->printRoutes();
    }

    /**
     * @param string $model
     *
     * @return string
     */
    private function createViewDirectory(string $model): string
    {
        $directory = Config::get('laravel-bakery.view.view_path') . '/' . $model;

        if (!File::isDirectory($directory)) {
            File::makeDirectory($directory);
        }

        return $directory;
    }

    /**
     * @return string
     */
    private function chooseModel(): string
    {
        return $this->choice('Model', $this->informationSchema->getTables());
    }

    /**
     * @return string
     */
    private function chooseView(): string
    {
        $views = [
            'overview',
        ];

        return $this->choice('View', $views, 0);
    }

    /**
     * @param string $namespace
     *
     * @return string
     */
    private function namespaceToPath(string $namespace): string
    {
        $parts = explode('\\', $namespace);
        if ($parts[0] === 'App') {
            $parts[0] = 'app';
        }

        return implode('/', $parts);
    }

    /**
     * @throws \Throwable
     */
    private function writeView(): void
    {
        $path   = $this->createViewDirectory($this->table);
        $writer = new ViewWriter($this->table, $this->informationSchema->getColumns($this->table));

        $this->info($writer->writeOverview(sprintf('%s/overview.blade.php', $path)));
        $this->info($writer->writeDetail(sprintf('%s/detail.blade.php', $path)));
    }

    /**
     * @throws \Throwable
     */
    private function writeController(): void
    {
        $writer = new ControllerWriter(new Model(Model::table2model($this->table), $this->table));
        $writer->setNamespace($this->namespace);

        $this->info($writer->write(base_path(sprintf(
            '%s/%sController.php',
            $this->namespaceToPath($this->namespace),
            $this->modelName
        ))));
    }

    /**
     * @return void
     * @throws \Throwable
     */
    private function printRoutes(): void
    {
        $this->info(PHP_EOL . '--- Routes -----------------------------------------------------------------------' . PHP_EOL);

        $this->info(view('laravel-bakery::routes')
            ->with('routeGroupName', $this->table)
            ->with('controllerClass', sprintf(
                '%s\\%sController::class',
                $this->namespace,
                $this->modelName
            ))
            ->render());

        $this->info(PHP_EOL . '----------------------------------------------------------------------------------' . PHP_EOL);
    }
}

<?php declare(strict_types=1);

namespace Simlux\LaravelBakery\Console\Commands;

use Artisan;
use Carbon\Carbon;
use Config;
use Simlux\LaravelBakery\Model\DataTypes\AbstractDataType;
use Simlux\LaravelBakery\Model\DataTypes\DateDataType;
use Simlux\LaravelBakery\Model\DataTypes\IntegerDataType;
use Simlux\LaravelBakery\Model\IndexInteraction;
use Simlux\LaravelBakery\Model\Model;
use Simlux\LaravelBakery\Model\ModelProperty;
use Simlux\LaravelBakery\Model\ModelPropertyInteraction;
use Simlux\LaravelBakery\Writer\FactoryWriter;
use Simlux\LaravelBakery\Writer\MigrationWriter;
use Simlux\LaravelBakery\Writer\ModelWriter;
use Simlux\LaravelBakery\Writer\RepositoryWriter;
use Simlux\LaravelBakery\Writer\SeederWriter;
use Str;

/**
 * Class ModelCommand
 *
 * @package Simlux\LaravelBakery\Console\Commands
 */
class ModelCommand extends AbstractCommand
{
    /**
     * @var string
     */
    protected $signature = 'bake:model {model} 
                                       {--all}
                                       {--repository}
                                       {--migration}
                                       {--factory}
                                       {--seeder}
                                       {--run-migration}';

    /**
     * @var string
     */
    protected $description = '...';

    /**
     * @var Model $model
     */
    private $model;

    /**
     * @return void
     */
    public function handle(): void
    {
        $this->clearScreen();
        $this->welcome();

        $this->model = new Model($this->argument('model'));
        $this->model->setTable($this->ask('Table name', $this->model->getTable()));

        $this->askForColumns();
        $this->addTimestamps();
        $this->showColumns();

        $interaction = new IndexInteraction($this);
        while ($this->confirm('Add index')) {
            $this->model->addIndex($interaction->getIndex());
        }

        $this->clearScreen();
        $this->writeFiles();

        $this->runMigration();

        $output = [];
        exec('composer dump-autoload', $output);
        collect($output)->each(function (string $line) {
            $this->info($line);
        });
    }

    private function askForColumns(): void
    {
        $first = true;
        $skip  = [];
        while ($first || $this->askForColumn($first)) {

            $this->info('Column definitions:');

            $interaction = new ModelPropertyInteraction($this, $first, $skip);
            $property    = $interaction->getProperty($skip);
            $this->model->addProperty($property);

            if ($property->dataType instanceof IntegerDataType && $property->dataType->isAutoIncrement()) {
                $skip[] = AbstractDataType::STEP_AUTOINCREMENT;
            }

            $first = $this->switchIf($first);
            $this->clearScreen();
        }
    }

    private function writeFiles(): void
    {
        $this->info('Written files:');
        try {
            $this->writeModel();
            $this->writeRepository();
            $this->writeMigration();
            $this->writeFactory();
            $this->writeSeeder();
        } catch (\Throwable $e) {
            $this->error($e->getMessage());
        }
    }

    /**
     * @param bool  $first
     *
     * @return bool
     */
    private function askForColumn(bool $first): bool
    {
        $this->clearScreen();
        if (!$first) {
            $this->showColumns();
        }

        return $this->confirm('Add column?', true);
    }

    private function welcome(): void
    {
        collect([
            '------------------------------------------------------------',
            '------------------------------------------------------------',
            '### Welcome to the bakery',
            '------------------------------------------------------------',
            '------------------------------------------------------------',
        ])->each(function (string $line) {
            $this->info($line);
        });
    }

    /**
     * @param bool $condition
     *
     * @return bool
     */
    private function switchIf(bool $condition): bool
    {
        if ($condition) {
            return !$condition;
        }

        return $condition;
    }

    private function addTimestamps(): void
    {
        $this->clearScreen();

        if ($this->confirm('Add timestamps?', false)) {

            /** @var DateDataType $dataType */
            $dataType                           = new DateDataType();
            $dataType->type                     = 'timestamp';
            $dataType->nullable                 = false;
            $dataType->currentTimestamp         = true;
            $dataType->onUpdateCurrentTimestamp = true;

            $createdProperty           = new ModelProperty();
            $createdProperty->name     = 'created_at';
            $createdProperty->dataType = $dataType;
            $this->model->addProperty($createdProperty);

            $updatedProperty           = new ModelProperty();
            $updatedProperty->name     = 'updated_at';
            $updatedProperty->dataType = $dataType;
            $this->model->addProperty($updatedProperty);
        }

        $this->clearScreen();
    }

    /**
     * @throws \Throwable
     */
    private function writeModel(): void
    {
        $this->model->setExtends(\Illuminate\Database\Eloquent\Model::class);

        $writer = new ModelWriter($this->model);
        $writer->setPath(Config::get('laravel-bakery.model.model_path'));
        $writer->setParentClass(\Illuminate\Database\Eloquent\Model::class);

        $this->info($writer->write($this->model->getName()));
    }

    /**
     * @throws \Throwable
     */
    private function writeRepository(): void
    {
        if ($this->option('all') || $this->option('repository') || $this->confirm('Write repository?')) {
            $writer = new RepositoryWriter($this->model);
            $writer->setPath(Config::get('laravel-bakery.model.repository_path'));

            $this->info($writer->write($this->model->getName() . 'Repository'));
        }
    }

    /**
     * @throws \Throwable
     */
    private function writeMigration(): void
    {
        if ($this->option('all') || $this->option('migration') || $this->confirm('Write migration?')) {
            $writer = new MigrationWriter($this->model);
            $writer->setPath(Config::get('laravel-bakery.model.migration_path'));

            $this->info($writer->write(sprintf(
                '%s_create_%s_table.php',
                Carbon::now()->format('Y_m_d_His'),
                $this->model->getTable()
            )));
        }
    }

    public function showColumns(): void
    {
        $headers = [
            '#',
            'Name',
            'Type',
            'Length',
            'Unsigned',
            'Nullable',
            'Default',
            'Extra',
        ];
        $this->table($headers, $this->getColumns());
    }

    public function getColumns(): array
    {
        return collect($this->model->getProperties())->map(function (ModelProperty $property, int $i) {
            return $property->dataType->getInfoForTable($i, $property->name);
        })->toArray();
    }

    /**
     * @throws \Throwable
     */
    private function writeSeeder(): void
    {
        if ($this->option('all') || $this->option('seeder') || $this->confirm('Write seeder?')) {
            $writer = new SeederWriter($this->model);
            $writer->setPath(Config::get('laravel-bakery.model.seeder_path'));

            $this->info($writer->write(sprintf('%sTableSeeder.php', Str::plural($this->model->getName()))));
        }
    }

    /**
     * @throws \Throwable
     */
    private function writeFactory(): void
    {
        if ($this->option('all') || $this->option('factory') || $this->confirm('Write factory?')) {
            $writer = new FactoryWriter($this->model);
            $writer->setPath(Config::get('laravel-bakery.model.factory_path'));

            $this->info($writer->write(sprintf('%sFactory.php', $this->model->getName())));
        }
    }

    private function runMigration(): void
    {
        if ($this->option('run-migration') || $this->confirm('Run migration?', false)) {
            $this->info('Running migration...');
            Artisan::call('migrate', []);
            collect(explode(PHP_EOL, Artisan::output()))->each(function (string $line) {
                $this->info($line);
            });
        }
    }
}

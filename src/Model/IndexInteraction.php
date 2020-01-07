<?php declare(strict_types=1);

namespace Simlux\LaravelBakery\Model;

use Simlux\LaravelBakery\Console\Commands\ModelCommand;

/**
 * Class IndexInteraction
 *
 * @package Simlux\LaravelBakery\Model
 */
class IndexInteraction extends AbstractInteraction
{
    /**
     * ModelPropertyInteraction constructor.
     *
     * @param ModelCommand $command
     */
    public function __construct(ModelCommand $command)
    {
        $this->command = $command;
    }

    public function getIndex(): Index
    {
        $this->command->clearScreen();
        $index = new Index();

        $index->type = $this->command->choice('Index type', ['index', 'unique'], 0);

        $this->command->showColumns();

        $columns  = $this->command->getColumns();
        $integers = $this->getIntegerFromString($this->command->askRequired('Choose columns'));

        $suggestName = null;
        if (count($integers) === 1) {
            $suggestName = $columns[current($integers)][1];
        }

        $index->name    = $this->command->askRequired('Index name', $suggestName);
        $index->columns = collect($integers)->map(function (int $i) use ($columns) {
            return $columns[$i][1];
        })->toArray();

        return $index;
    }

    /**
     * @param string $string
     *
     * @return array
     */
    private function getIntegerFromString(string $string): array
    {
        return explode(',', $string);
    }
}
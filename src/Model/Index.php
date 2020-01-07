<?php declare(strict_types=1);

namespace Simlux\LaravelBakery\Model;

/**
 * Class Index
 *
 * @package Simlux\LaravelBakery\Model
 */
class Index
{
    /**
     * @var array
     */
    public $columns = [];

    /**
     * @var string
     */
    public $name;

    /**
     * @var string
     */
    public $type;

    /**
     * @return string
     */
    public function getIndexMigrationString(): string
    {
        $columnString = collect($this->columns)->map(function (string $column) {
            return sprintf("'%s'", $column);
        })->implode(', ');

        return sprintf("\$table->%s([%s], '%s');", $this->type, $columnString, $this->name);
    }
}
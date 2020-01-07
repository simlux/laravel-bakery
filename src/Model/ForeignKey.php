<?php declare(strict_types=1);

namespace Simlux\LaravelBakery\Model;

/**
 * Class ForeignKey
 *
 * @package Simlux\LaravelBakery\Model
 */
class ForeignKey
{
    /**
     * @var string
     */
    public $table;

    /**
     * @var string
     */
    public $column;

    /**
     * @param string $name
     *
     * @return string
     */
    public function getIndexMigrationString(string $name): string
    {
        return sprintf("\$table->index('%s', '%s');", $name, $name);
    }

    /**
     * @param string $name
     *
     * @return string
     */
    public function getForeignKeyMigrationString(string $name): string
    {
        return sprintf("MigrationHelper::createForeignKey(\$table, '%s');", $name);
    }
}


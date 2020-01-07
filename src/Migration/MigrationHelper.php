<?php declare(strict_types=1);

namespace Simlux\LaravelBakery\Migration;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Expression;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class MigrationHelper
{
    const REFERENTIAL_ACTION_CASCADE     = 'CASCADE';
    const REFERENTIAL_ACTION_RESTRICT    = 'RESTRICT';
    const REFERENTIAL_ACTION_SET_NULL    = 'SET NULL';
    const REFERENTIAL_ACTION_NO_ACTION   = 'NO ACTION';
    const REFERENTIAL_ACTION_SET_DEFAULT = 'SET DEFAULT';

    /**
     * @param Blueprint   $table
     * @param null|string $comment
     */
    public static function uuid(Blueprint $table, string $comment = null)
    {
        $table->char('uuid', 36);
        $table->unique('uuid', 'uuid');
        if (!is_null($comment)) {
            $table->comment = $comment;
        }
    }

    /**
     * @param Blueprint $table
     * @param string    $column
     */
    public static function json(Blueprint $table, string $column)
    {
        $table->text($column);
    }

    /**
     * @param Blueprint $table
     * @param string    $column
     */
    public static function addCreatedAt(Blueprint $table, string $column = Model::CREATED_AT)
    {
        self::addTimestampColumn($table, $column, true, false);
    }

    /**
     * @param Blueprint $table
     * @param string    $column
     */
    public static function addUpdatedAt(Blueprint $table, string $column = Model::UPDATED_AT)
    {
        self::addTimestampColumn($table, $column, true, true);
    }

    /**
     * @param Blueprint $table
     * @param string    $column
     * @param bool      $setDefault
     * @param bool      $setOnUpdate
     */
    private static function addTimestampColumn(Blueprint $table, string $column, bool $setDefault = true, bool $setOnUpdate = false)
    {
        if ($setDefault && !$setOnUpdate) {
            $table->timestamp($column)->default(new Expression('CURRENT_TIMESTAMP'));
        } else {
            if ($setDefault && $setOnUpdate) {
                $table->timestamp($column)->default(new Expression('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));
            } else {
                $table->timestamp($column);
            }
        }
    }

    /**
     * @param Blueprint $table
     * @param string    $column
     * @param string    $onDelete
     * @param string    $onUpdate
     */
    public static function createForeignKey(Blueprint $table, string $column, string $onDelete = self::REFERENTIAL_ACTION_RESTRICT, string $onUpdate = self::REFERENTIAL_ACTION_RESTRICT)
    {
        $columnParts = explode('_', $column);
        $count       = count($columnParts);
        $last        = array_pop($columnParts);
        if ($count >= 2 && $last === 'id') {
            $on         = self::getTableName($columnParts);
            $references = $last;
            $name       = self::getForeignKeyName($table, $column);

            $table->foreign($column, $name)
                ->references($references)
                ->on($on)
                ->onDelete($onDelete)
                ->onUpdate($onUpdate);
        }
    }

    /**
     * @param array $columnParts
     *
     * @return null|string
     */
    public static function getTableName(array $columnParts)
    {
        $modelName = implode('_', $columnParts);
        $table     = null;

        switch (substr($modelName, -1)) {
            case 'y':
                $table = substr($modelName, 0, -1) . 'ies';
                break;

            case 's':
                $table = $modelName;
                break;

            case 'h':
                $table = $modelName . 'es';
                break;

            default:
                $table = $modelName . 's';
        }

        return $table;
    }

    /**
     * @param Blueprint $table
     * @param string    $column
     */
    public static function dropForeignKey(Blueprint $table, string $column)
    {
        $columnParts = explode('_', $column);
        if (count($columnParts) === 2 && $columnParts[1] === 'id') {
            $name = self::getForeignKeyName($table, $column);
            if (Schema::hasColumn($table->getTable(), $column)) {
                $table->dropForeign($name);
                $table->dropColumn($column);
            }
        }
    }

    /**
     * @param Blueprint $table
     * @param string    $column
     *
     * @return string
     */
    public static function getForeignKeyName(Blueprint $table, string $column): string
    {
        return sprintf('fk__%s__%s', $table->getTable(), $column);
    }

    /**
     * @param Blueprint $table
     */
    public static function addSoftDelete(Blueprint $table)
    {
        $table->softDeletes();
        $table->index(['deleted_at'], 'deleted_at');
    }

    /**
     * @param Blueprint $table
     */
    public static function dropSoftDelete(Blueprint $table)
    {
        $table->dropIndex('deleted_at');
        $table->dropSoftDeletes();
    }

}
<?php declare(strict_types=1);

namespace Simlux\LaravelBakery\Model;

use DB;

/**
 * Class InformationSchema
 *
 * @package Simlux\LaravelBakery\Model
 */
class InformationSchema
{
    /**
     * @var string
     */
    private $connection;

    /**
     * @var string
     */
    private $database;

    public function __construct()
    {
        $this->connection = (string) config('database.default');
        $this->database   = (string) config(sprintf('database.connections.%s.database', $this->connection));
    }

    /**
     * @param array $ignore
     *
     * @return array
     */
    public function getTables(array $ignore = ['migrations', 'failed_jobs', 'password_resets']): array
    {
        return DB::connection($this->connection)
            ->table('INFORMATION_SCHEMA.TABLES')
            ->select(['TABLE_NAME AS table'])
            ->where('TABLE_SCHEMA', $this->database)
            ->whereNotIn('TABLE_NAME', $ignore)
            ->orderBy('TABLE_NAME')
            ->get()
            ->map(function (\stdClass $result) {
                return $result->table;
            })
            ->toArray();
    }

    /**
     * @return array
     */
    public function getPrimaryKeys(): array
    {
        return DB::connection($this->connection)
            ->table('INFORMATION_SCHEMA.COLUMNS')
            ->select([
                'TABLE_NAME AS table',
                'COLUMN_NAME AS column',
            ])
            ->where('TABLE_SCHEMA', $this->database)
            ->where('COLUMN_KEY', 'PRI')
            ->orderBy('TABLE_NAME')
            ->orderBy('COLUMN_NAME')
            ->get()
            ->map(function (\stdClass $result) {
                return sprintf('%s.%s', $result->table, $result->column);
            })
            ->toArray();
    }

    /**
     * @param string $table
     *
     * @return array
     */
    public function getColumns(string $table): array
    {
        return DB::connection($this->connection)
            ->table('INFORMATION_SCHEMA.COLUMNS')
            ->select([
                'COLUMN_NAME AS column',
            ])
            ->where('TABLE_SCHEMA', $this->database)
            ->where('TABLE_NAME', $table)
            ->orderBy('ORDINAL_POSITION')
            ->get()
            ->map(function (\stdClass $result) {
                return $result->column;
            })
            ->toArray();
    }

    /**
     * @param string $table
     * @param string $column
     *
     * @return array
     */
    public function getColumnSpecs(string $table, string $column): array
    {
        return DB::connection($this->connection)
            ->table('INFORMATION_SCHEMA.COLUMNS')
            ->select([
                'TABLE_NAME AS table',
                'COLUMN_NAME AS column',
                'DATA_TYPE AS datatype',
                'NUMERIC_SCALE AS scale',
                'COLUMN_DEFAULT AS default',
                'COLUMN_TYPE AS column_type',
            ])
            ->selectRaw('IF(IS_NULLABLE = "YES", 1, 0) AS `nullable`')
            ->selectRaw('IF(NUMERIC_PRECISION IS NOT NULL, NUMERIC_PRECISION, CHARACTER_MAXIMUM_LENGTH) AS `length`')
            ->selectRaw('IF(COLUMN_TYPE LIKE "%unsigned", 1, 0) AS `unsigned`')
            ->where('TABLE_SCHEMA', $this->database)
            ->where('TABLE_NAME', $table)
            ->where('COLUMN_NAME', $column)
            ->orderBy('TABLE_NAME')
            ->orderBy('COLUMN_NAME')
            ->get()
            ->map(function (\stdClass $result) {
                return (array) $result;
            })
            ->first();
    }
}
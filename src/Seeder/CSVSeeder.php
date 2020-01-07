<?php

namespace Simlux\LaravelBakery\Seeder;

use Illuminate\Database\Seeder;

/**
 * Class CSVSeeder
 */
abstract class CSVSeeder extends Seeder
{
    /**
     * @return void
     */
    abstract public function run(): void;

    /**
     * @return array
     */
    public function getData(): array
    {
        $header = null;
        $rows   = [];

        $first  = false;
        $handle = fopen($this->getCSVFile(), 'r');
        if ($handle) {

            while (($row = fgetcsv($handle, 1000, ',')) !== false) {
                if ($first === false) {
                    $header = $row;
                    $first  = true;
                    continue;
                }

                $rows[] = $this->getAssocArray($header, $row);
            }

            fclose($handle);
        }

        return $rows;
    }

    /**
     * @param array $header
     * @param array $data
     *
     * @return array
     */
    private function getAssocArray(array $header, array $data): array
    {
        $row = [];
        foreach ($data as $i => $item) {
            $row[$header[$i]] = $item;
        }

        return $row;
    }

    /**
     * @return string
     */
    abstract protected function getCSVFile(): string;
}

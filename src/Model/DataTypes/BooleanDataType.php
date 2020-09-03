<?php declare(strict_types=1);

namespace Simlux\LaravelBakery\Model\DataTypes;

use Simlux\LaravelBakery\Console\Commands\AbstractCommand;

/**
 * Class BooleanDataType
 *
 * @package Simlux\LaravelBakery\Model\DataType
 */
class BooleanDataType extends AbstractDataType
{
    /**
     * @var array
     */
    public static $types = [];

    /**
     * @var int
     */
    protected $defaultType = 0;

    /**
     * @param AbstractCommand $command
     * @param array           $skip
     *
     * @return void
     */
    public function interact(AbstractCommand $command, array $skip = []): void
    {

    }

    /**
     * @param array $info
     *
     * @return void
     */
    public function processInfo(array $info): void
    {
        parent::processInfo($info);

        // @TODO: process tinyint(1)
    }

    /**
     * @return string
     */
    public function getPhpType(): string
    {
        return 'bool';
    }

    /**
     * @return string
     */
    public function getMethodName(): string
    {
        return 'boolean';
    }

    /**
     * @param int    $i
     * @param string $name
     *
     * @return array ['Name', 'Type', 'Length', 'Unsigned', 'Nullable', 'Default', 'Extra']
     */
    public function getInfoForTable(int $i, string $name): array
    {
        return [
            $i,
            $name,
            $this->type,
            null,
            null,
            $this->nullable ? 'yes' : 'no',
            $this->default,
            null,
        ];
    }
}
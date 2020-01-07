<?php declare(strict_types=1);

namespace Simlux\LaravelBakery\Model\DataTypes;

use Simlux\LaravelBakery\Console\Commands\AbstractCommand;

/**
 * Class DateDataType
 *
 * @package Simlux\LaravelBakery\Model\DataType
 */
class DateDataType extends AbstractDataType
{
    /**
     * @var array
     */
    public static $types = [
        'timestamp',
        'datetime',
        'date',
        'time',
    ];

    /**
     * @var int
     */
    protected $defaultType = 0;

    /**
     * @var bool
     */
    public $currentTimestamp = false;

    /**
     * @var bool
     */
    public $onUpdateCurrentTimestamp = false;

    /**
     * @param AbstractCommand $command
     * @param array           $skip
     *
     * @return void
     */
    public function interact(AbstractCommand $command, array $skip = []): void
    {
        $this->type                     = $command->choice('Specify data type', self::$types, $this->getDefaultType());
        $this->nullable                 = $command->confirm('Nullable', $this->nullable);
        $this->currentTimestamp         = $command->confirm('Set CURRENT TIMESTAMP on CREATE?', $this->currentTimestamp);
        $this->onUpdateCurrentTimestamp = $command->confirm('Set CURRENT TIMESTAMP on UPDATE?', $this->onUpdateCurrentTimestamp);
    }

    /**
     * @param array $info
     *
     * @return void
     */
    public function processInfo(array $info): void
    {
        parent::processInfo($info);

        // @TODO:
        // - process CURRENT TIMESTAMP on CREATE
        // - process CURRENT TIMESTAMP on UPDATE
    }

    /**
     * @return string
     */
    public function getPhpType(): string
    {
        return 'Carbon';
    }

    /**
     * @return string
     * @throws \Exception
     */
    public function getMethodName(): string
    {
        $methodMap = [
            'timestamp' => 'timestamp',
            'datetime'  => 'dateTime',
            'date'      => 'date',
            'time'      => 'time',
        ];

        if (!isset($methodMap[$this->type])) {
            throw new \Exception(sprintf('Missing method name for "%s"', $this->type));
        }

        return $methodMap[$this->type];
    }

    /**
     * @param string $name
     *
     * @return string
     */
    public function getMethodParams(string $name): string
    {
        return $this->paramToString($name);
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
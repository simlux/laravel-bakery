<?php declare(strict_types=1);

namespace Simlux\LaravelBakery\Model\DataTypes;

use Simlux\LaravelBakery\Console\Commands\AbstractCommand;

/**
 * Class FloatDataType
 *
 * @package Simlux\LaravelBakery\Model\DataType
 */
class FloatDataType extends AbstractDataType
{
    /**
     * @var array
     */
    public static $types = [
        'decimal',
        'float',
    ];

    /**
     * @var int
     */
    protected $defaultType = 0;

    /**
     * @var int
     */
    public $length = 5;

    /**
     * @var int
     */
    public $precision = 2;

    /**
     * @var bool
     */
    public $unsigned = false;

    /**
     * @param AbstractCommand $command
     * @param array           $skip
     *
     * @return void
     */
    public function interact(AbstractCommand $command, array $skip = []): void
    {
        $this->type      = $command->choice('Specify data type', self::$types, $this->getDefaultType());
        $this->length    = (int) $command->ask('Length (without precision)', $this->length);
        $this->precision = (int) $command->ask('Precision', $this->precision);
        $this->unsigned  = $command->confirm('Unsigned', $this->unsigned);
        $this->nullable  = $command->confirm('Nullable', $this->nullable);

        if ($command->confirm('Default', false)) {
            $this->default = (float) $command->ask('Type in default value');
        }
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
        return 'float';
    }

    /**
     * @return string
     * @throws \Exception
     */
    public function getMethodName(): string
    {
        $methodMap = [
            'decimal' => 'decimal',
            'float'   => 'float',
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
        $params = [
            $this->paramToString($name),
            $this->getTotal(),
            $this->precision
        ];

        return implode(', ', $params);
    }

    private function getTotal(): int
    {
        $total = $this->length + $this->precision;

        if (!$this->unsigned) {
            $total++;
        }

        return $total;
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
            $name,
            $this->type,
            sprintf('%s,%s', $this->getTotal(), $this->precision),
            $this->unsigned ? 'yes' : 'no',
            $this->nullable ? 'yes' : 'no',
            $this->default,
            null,
        ];
    }
}
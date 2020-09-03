<?php declare(strict_types=1);

namespace Simlux\LaravelBakery\Model\DataTypes;

use Simlux\LaravelBakery\Console\Commands\AbstractCommand;

/**
 * Class IntegerDataType
 *
 * @package Simlux\LaravelBakery\Model\DataType
 */
class IntegerDataType extends AbstractDataType
{
    /**
     * @var array
     */
    public static $types = [
        'tinyint',
        'smallint',
        'mediumint',
        'int',
        'bigint',
    ];

    /**
     * @var int
     */
    protected $defaultType = 3;

    /**
     * @var bool
     */
    protected $unsigned = true;

    /**
     * @var bool
     */
    protected $autoIncrement = false;

    /**
     * @param AbstractCommand $command
     * @param array           $skip
     *
     * @return void
     */
    public function interact(AbstractCommand $command, array $skip = []): void
    {
        $this->type = $command->choice('Specify data type', self::$types, $this->getDefaultType());

        if (!$this->skip(self::STEP_AUTOINCREMENT, $skip)) {
            $this->autoIncrement = $command->confirm('Auto Increment', true);
        }

        $this->unsigned = $command->confirm('Unsigned', $this->unsigned);
        $this->nullable = $command->confirm('Nullable', $this->nullable);

        if ($command->confirm('Default', false)) {
            $this->default = (int) $command->ask('Type in default value');
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

        $this->unsigned = $info['unsigned'] === 1;
    }

    /**
     * @return string
     */
    public function getPhpType(): string
    {
        return 'int';
    }

    /**
     * @return bool
     */
    public function isAutoIncrement(): bool
    {
        return $this->autoIncrement;
    }

    /**
     * @return string
     * @throws \Exception
     */
    public function getMethodName(): string
    {
        $methodMap = [
            'tinyint'   => 'tinyInteger',
            'smallint'  => 'smallInteger',
            'mediumint' => 'mediumInteger',
            'int'       => 'integer',
            'bigint'    => 'bigInteger',
        ];

        if (!isset($methodMap[$this->type])) {
            throw new \Exception(sprintf('Missing method name for "%s"', $this->type));
        }

        $methodName = $methodMap[$this->type];

        if ($this->unsigned) {
            $methodName = 'unsigned' . ucfirst($methodName);
        }

        return $methodName;
    }

    /**
     * @param string $name
     *
     * @return string
     */
    public function getMethodParams(string $name): string
    {
        $params = [sprintf('self::PROPERTY_%s', strtoupper($name))];

        if ($this->autoIncrement) {
            $params[] = $this->paramToString(true);
        }

        return implode(', ', $params);
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
            $this->unsigned ? 'yes' : 'no',
            $this->nullable ? 'yes' : 'no',
            $this->default,
            $this->autoIncrement ? 'auto_increment' : null,
        ];
    }
}
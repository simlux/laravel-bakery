<?php declare(strict_types=1);

namespace Simlux\LaravelBakery\Model\DataTypes;

use Simlux\LaravelBakery\Console\Commands\AbstractCommand;

/**
 * Class StringDataType
 *
 * @package Simlux\LaravelBakery\Model\DataType
 */
class StringDataType extends AbstractDataType
{
    /**
     * @var array
     */
    public static $types = [
        'char',
        'varchar',
    ];

    /**
     * @var int
     */
    protected $defaultType = 1;

    /**
     * @var int
     */
    protected $length = 50;

    /**
     * @param AbstractCommand $command
     * @param array           $skip
     *
     * @return void
     */
    public function interact(AbstractCommand $command, array $skip = []): void
    {
        $this->type     = $command->choice('Specify data type', self::$types, $this->getDefaultType());
        $this->length   = (int) $command->ask('Length', $this->length);
        $this->nullable = $command->confirm('Nullable', $this->nullable);
        if ($command->confirm('Default', false)) {
            $this->default = $command->ask('Type in default value');
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

        $this->length = (int) $info['length'];
    }

    /**
     * @return string
     */
    public function getPhpType(): string
    {
        return 'string';
    }

    /**
     * @return string
     * @throws \Exception
     */
    public function getMethodName(): string
    {
        $methodMap = [
            'char'    => 'char',
            'varchar' => 'string',
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
            $this->length
        ];

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
            $this->length,
            null,
            $this->nullable ? 'yes' : 'no',
            $this->default,
            null,
        ];
    }
}
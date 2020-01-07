<?php declare(strict_types=1);

namespace Simlux\LaravelBakery\Console\Commands;

use Illuminate\Console\Command;

/**
 * Class AbstractCommand
 *
 * @package Simlux\LaravelBakery\Console\Commands
 */
abstract class AbstractCommand extends Command
{
    /**
     * @var string
     */
    protected $signature;

    /**
     * @var string
     */
    protected $description;

    /**
     * @return void
     */
    abstract public function handle(): void;

    /**
     * @return void
     */
    public function clearScreen(): void
    {
        $this->getOutput()->write("\033\143");
    }

    /**
     * @param             $question
     * @param null        $default
     * @param string|null $errorMessage
     *
     * @return string
     */
    public function askRequired($question, $default = null, string $errorMessage = null): string
    {
        $answer = $this->ask($question, $default);

        if (is_null($answer)) {
            $this->error($errorMessage ?? 'Answer is required!');
            $answer = $this->askRequired($question, $default);
        }

        return $answer;
    }
}

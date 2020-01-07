<?php declare(strict_types=1);

namespace Simlux\LaravelBakery\Writer;

use Str;

/**
 * Class AbstractWriter
 *
 * @package Simlux\LaravelBakery\Writer
 */
abstract class AbstractWriter
{
    const TAB = "\t";
    const EOL = PHP_EOL;

    /**
     * @var string
     */
    protected $path;

    /**
     * @var string
     */
    protected $template;

    /**
     * @var array
     */
    protected $vars = [];

    /**
     * @var bool
     */
    protected $declare = false;

    /**
     * @var string
     */
    protected $namespace;

    /**
     * @var array
     */
    protected $uses = [];

    /**
     * @var string
     */
    protected $parentClass;

    /**
     * @var \Simlux\LaravelBakery\Model\Model
     */
    protected $model;

    /**
     * ClassWriter constructor.
     *
     * @param \Simlux\LaravelBakery\Model\Model $model
     */
    public function __construct(\Simlux\LaravelBakery\Model\Model $model)
    {
        $this->model = $model;
    }

    /**
     * @param string $file
     *
     * @return bool|int
     * @throws \Throwable
     */
    public function write(string $file)
    {
        $this->beforeWrite();

        if ($this->parentClass) {
            $this->useClass($this->parentClass);
        }

        $this->setVar('uses', $this->getUseString());

        if (!Str::endsWith($file, '.php')) {
            $file .= '.php';
        }

        $view = view(sprintf('laravel-bakery::%s', $this->template));
        collect($this->vars)->each(function ($value, $key) use ($view) {
            $view->with($key, $value);
        });

        $prefix = '<?php';
        if ($this->declare) {
            $prefix .= ' declare(strict_types=1);';
        }
        $prefix .= PHP_EOL . PHP_EOL;

        $file = $this->path . $file;

        if (\File::put($file, $prefix . $view->render())) {
            return $file;
        }

        return false;
    }

    /**
     * @return void
     */
    protected function beforeWrite(): void
    {

    }

    /**
     * @param string $key
     * @param mixed  $value
     */
    public function setVar(string $key, $value): void
    {
        $this->vars[$key] = $value;
    }

    /**
     * @param string $key
     *
     * @return mixed
     */
    public function getVar(string $key)
    {
        if (isset($this->vars[$key])) {
            return $this->vars[$key];
        }
    }

    /**
     * @param string $namespace
     */
    public function setNamespace(string $namespace): void
    {
        $this->namespace = $namespace;
    }

    /**
     * @param bool $declare
     */
    public function setDeclare(bool $declare = true): void
    {
        $this->declare = $declare;
    }

    /**
     * @param string $path
     */
    public function setPath(string $path): void
    {
        $this->path = $path;

        if (!Str::endsWith($path, '/')) {
            $this->path .= '/';
        }
    }

    /**
     * @param string $template
     */
    public function setTemplate(string $template): void
    {
        $this->template = $template;
    }

    /**
     * @param string $class
     */
    public function useClass(string $class): void
    {
        if (!in_array($class, $this->uses) && !$this->assertInNamespace($class, $this->namespace)) {
            $this->uses[] = $class;
        }
    }

    /**
     * @return string|null
     */
    public function getParentClass()
    {
        if ($this->parentClass) {
            return last(explode('\\', $this->parentClass));
        }
    }

    /**
     * @param string $parentClass
     */
    public function setParentClass(string $parentClass): void
    {
        $this->parentClass = $parentClass;
    }

    /**
     * @return string
     */
    private function getUseString(): string
    {
        return collect($this->uses)->map(function (string $use) {
            return sprintf('use %s;', $use);
        })->implode(PHP_EOL);
    }

    /**
     * @param string $class
     * @param string|null $namespace
     *
     * @return bool
     */
    protected function assertInNamespace(string $class, string $namespace = null): bool
    {
        if (is_null($namespace)) {
            return false;
        }

        $classParts = explode('\\', $class);
        array_pop($classParts);

        return $namespace === implode('\\', $classParts);
    }
}

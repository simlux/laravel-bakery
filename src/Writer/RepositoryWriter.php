<?php declare(strict_types=1);

namespace Simlux\LaravelBakery\Writer;

/**
 * Class RepositoryWriter
 *
 * @package Simlux\LaravelBakery\Writer
 */
class RepositoryWriter extends AbstractWriter
{
    /**
     * @var string
     */
    protected $template = 'repository';

    /**
     * @var bool
     */
    protected $declare = true;

    /**
     * @var string
     */
    protected $namespace = 'App\\Models\\Repositories';

    /**
     * @param string $namespace
     */
    public function setNamespace(string $namespace): void
    {
        $this->namespace = $namespace;
    }

    /**
     * @return void
     */
    protected function beforeWrite(): void
    {
        parent::beforeWrite();

        $this->setVar('model', $this->model);
        $this->setVar('namespace', $this->namespace);
        $this->setVar('class', $this->model->getName());

        if ($this->parentClass) {
            $this->useClass($this->parentClass);
        }
        $this->setVar('extends', $this->getParentClass());
    }
}
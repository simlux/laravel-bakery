<?php declare(strict_types=1);

namespace Simlux\LaravelBakery\Model;

class Method
{
    /**
     * @var string
     */
    public $access = 'public';

    /**
     * @var string
     */
    public $static = false;

    /**
     * @var string
     */
    public $name;

    /**
     * @var string
     */
    public $params = [];

    /**
     * @var string
     */
    public $return = 'void';

    /**
     * @var string
     */
    public $body = '';

    /**
     * @return string
     */
    public function toString(): string
    {

    }
}
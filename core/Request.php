<?php
/**
 * Created by PhpStorm.
 * User: snowgirl
 * Date: 4/13/17
 * Time: 5:25 PM
 */
namespace CORE;

/**
 * !!! Simple Abstract Request
 *
 * Class Request
 * @package CORE
 */
abstract class Request extends \stdClass
{
    protected $params = array();

    public function __set($key, $value)
    {
        $this->params[$key] = $value;
    }

    public function __get($key)
    {
        return isset($this->$key) ? $this->params[$key] : null;
    }

    public function __isset($key)
    {
        return isset($this->params[$key]);
    }

    /**
     * @return \Closure
     */
    abstract public function parse();
}
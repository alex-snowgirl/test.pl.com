<?php
/**
 * Created by PhpStorm.
 * User: snowgirl
 * Date: 4/14/17
 * Time: 1:53 AM
 */
namespace CORE;

/**
 * Class Exception
 * @package CORE
 */
class Exception extends \Exception
{
    protected $isLogged;

    public function setLogged()
    {
        $this->isLogged = true;
        return $this;
    }

    public function isLogged()
    {
        return $this->isLogged;
    }

    public function check($text)
    {
        return is_int(strpos($this->getMessage(), $text));
    }
}
<?php

/**
 * Created by PhpStorm.
 * User: snowgirl
 * Date: 4/19/17
 * Time: 9:19 PM
 */
namespace CORE;

/**
 * Simple Session Manager class
 * Encapsulates all func related to Sessions
 * @todo remove "extends \stdClass"
 *
 * Class Session
 * @package CORE
 */
class Session extends \stdClass
{
    public function __construct()
    {
        if (!session_id()) {
            session_start();
        }
    }

    public function has($k)
    {
        return isset($_SESSION[$k]);
    }

    public function __isset($k)
    {
        return $this->has($k);
    }


    public function set($k, $v)
    {
        $_SESSION[$k] = $v;
        return $this;
    }

    public function __set($k, $v)
    {
        $this->set($k, $v);
    }

    public function get($k, $default = null)
    {
        return isset($_SESSION[$k]) ? $_SESSION[$k] : $default;
    }

    public function __get($k)
    {
        return $this->get($k);
    }
}
<?php
/**
 * Created by PhpStorm.
 * User: snowgirl
 * Date: 4/14/17
 * Time: 1:53 AM
 */
namespace CORE;

/**
 * Simple observable class
 *
 * Class Observable
 * @package CORE
 */
class Observable
{
    protected $callbacks = array();

    public function on($event, \Closure $callback)
    {
        if (!isset($this->callbacks[$event])) {
            $this->callbacks[$event] = array();
        }

        $this->callbacks[$event][] = $callback;
        return $this;
    }

    public function off($event)
    {
        $this->callbacks[$event] = array();
        return $this;
    }

    public function trigger($event)
    {
        if (isset($this->callbacks[$event])) {
            $args = func_get_args();
            $args[0] = $this;

            foreach ($this->callbacks[$event] as $fn) {
                call_user_func_array($fn, $args);
            }
        }

        $this->off($event);
        return $this;
    }
}
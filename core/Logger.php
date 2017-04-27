<?php
/**
 * Created by PhpStorm.
 * User: snowgirl
 * Date: 4/25/17
 * Time: 3:18 PM
 */
namespace CORE;

/**
 * !!! Simple Logger class
 * @todo implement message types
 * @todo implement client id logging
 *
 * Class Logger
 * @package CORE
 */
abstract class Logger
{
    public function __construct(\stdClass $config)
    {
        foreach ($config as $k => $v) {
            $this->$k = $v;
        }

        $this->setLocation();
    }

    abstract public function setLocation();

    abstract public function log($msg);

    public function logException(Exception $ex)
    {
        if ($ex->isLogged()) {
            return $this;
        }

        $ex->setLogged();

        return $this->log(join("\r\n", array(
            $ex->getMessage(),
            $ex->getTraceAsString()
        )));
    }
}
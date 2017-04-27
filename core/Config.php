<?php
/**
 * Created by PhpStorm.
 * User: snowgirl
 * Date: 4/25/17
 * Time: 1:51 PM
 */
namespace CORE;

use Composer\Autoload\ClassLoader;

/**
 * !!! Simple Config class
 * !!! Simple service locator (Dependency Container)
 * @todo split into Config and DI classes
 * @todo implement FileConfig object instead of $fileLocation arg (Open-Closed & Liskov Subs SOLID principles)
 * @todo replace "parseFile" with FileConfig::parse
 *
 * Class Config
 * @package CORE
 * @property RDBMS $rdbms
 * @property Logger $logger
 * @property \stdClass $raw
 */
class Config extends \stdClass
{
    protected $loader;

    /**
     * @param ClassLoader $loader
     * @param $fileLocation
     */
    public function __construct(ClassLoader $loader, $fileLocation)
    {
        $this->loader = $loader;
        $this->loadConfig($fileLocation);
    }

    protected function parseFile($fileLocation)
    {
        return json_decode(json_encode(parse_ini_file($fileLocation, true)), false);
    }

    protected function loadConfig($fileLocation)
    {
        $tmp = new \stdClass();

        foreach ($this->parseFile($fileLocation) as $k => $v) {
            $tmp->$k = $v;
        }

        $this->raw = $tmp;

        return $this;
    }

    public function __get($k)
    {
        if ('rdbms' == $k) {
            $provider = $this->raw->app->{'rdbms.provider'};
            $name = 'CORE\\RDBMS\\' . $provider;
            $v = new $name($this->raw->{'rdbms.' . $provider}, $this->logger);
        } elseif ('logger' == $k) {
            $provider = $this->raw->app->{'logger.provider'};
            $name = 'CORE\\Logger\\' . $provider;
            $v = new $name($this->raw->{'logger.' . $provider});
        } else {
            $v = null;
        }

        $this->$k = $v;
        return $v;
    }

    public function get($k, $kk = null, $default = null, $replace = array())
    {
        if (array_key_exists($k, $this->data)) {
            $v = $this->data[$k];

            if ($kk) {
                if (is_array($v) && array_key_exists($kk, $v)) {
                    $tmp = $v[$kk];
                } else {
                    $tmp = $default;
                }
            } else {
                $tmp = $v;
            }
        } else {
            $tmp = $default;
        }

        if ($replace) {
            return str_replace(array_map(function ($i) {
                return '{' . $i . '}';
            }, array_keys($replace)), array_values($replace), $tmp);
        }

        return $tmp;
    }

    /**
     * @return ClassLoader
     */
    public function getLoader()
    {
        return $this->loader;
    }
}

class ConfigException extends Exception
{

}
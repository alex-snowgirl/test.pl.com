<?php
/**
 * Created by PhpStorm.
 * User: snowgirl
 * Date: 4/25/17
 * Time: 3:07 PM
 */
namespace CORE\Logger;

use CORE\Logger;

/**
 * !!! Simple Logger class
 *
 * Class Logger
 * @package CORE
 */
class Txt extends Logger
{
    protected $file;
    protected $dir;

    /**
     * @todo improve...
     * @return mixed
     */
    protected function getDir()
    {
        return str_replace('/core/Logger', '', __DIR__);
    }

    public function setLocation()
    {
        $v = $this->getDir() . '/' . $this->file;

        if (file_exists($v) && is_writable($v)) {
            $this->file = $v;
            ini_set('error_log', $this->file);
        }

        return $this;
    }

    public function log($msg)
    {
        $this->_log(join(' ', array(
            time(),
            $msg
        )));

        return $this;
    }

    protected function _log($v = '')
    {
        if ($this->file) {
            error_log($v . "\n", 3, $this->file);
        }
    }
}
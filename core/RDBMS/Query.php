<?php
/**
 * Created by PhpStorm.
 * User: snowgirl
 * Date: 4/25/17
 * Time: 3:35 PM
 */
namespace CORE\RDBMS;

use CORE\RDBMS;
use CORE\Exception;

/**
 * Class Query
 * @package CORE\RDBMS
 * @property integer $found_rows
 * @property integer $affected_rows
 * @property integer $errno
 * @property mixed $insert_id
 * @property array $errin
 */
abstract class Query implements \SeekableIterator, \Countable, \ArrayAccess
{
    protected $query;
    protected $bind;
    /** @var RDBMS */
    protected $provider;

    /**
     * @param RDBMS $provider
     * @param $query
     * @param array $bind
     */
    public function __construct(RDBMS $provider, $query, $bind = array())
    {
        $this->provider = $provider;
        $this->query = $query;
        $this->bind = $bind;
        $this->execute();
    }

    abstract public function execute();

    abstract public function getArray($key = null);

    public function __get($k)
    {
        return $this->provider->$k;
    }
}

class RdbmsQueryException extends Exception
{

}
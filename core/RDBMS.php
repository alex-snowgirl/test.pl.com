<?php

/**
 * Created by PhpStorm.
 * User: snowgirl
 * Date: 4/25/17
 * Time: 2:05 PM
 */
namespace CORE;

use CORE\RDBMS\Query;

/**
 * Class RDBMS
 * @package CORE
 */
abstract class RDBMS
{
    protected $logger;

    public function __construct(\stdClass $config, Logger $logger)
    {
        foreach ($config as $k => $v) {
            $this->$k = $v;
        }

        $this->logger = $logger;
    }

    abstract protected function query($query, $bind);

    /**
     * @param $query
     * @param $bind
     * @return Query
     */
    abstract protected function execute($query, $bind);

    abstract public function quote($v, $table = '');

    abstract public function buildSelectSQL($input = '*', $isFoundRows = false, $table = '');

    abstract public function buildFromSQL($tables);

    abstract public function buildIndexSQL($index);

    abstract public function buildWhereSQL($where = null, array &$bind, $table = '');

    abstract public function buildGroupSQL($group = null, $table = '');

    abstract public function buildOrderSQL($order = null, $table = '');

    abstract public function buildLimitSQL(&$limit = null, array &$bind);

    abstract public function buildHavingSQL($where = null, array &$bind);

    /**
     * @param $table
     * @param array $values
     * @return Query
     */
    abstract public function create($table, array $values);

    /**
     * @param $table
     * @param $where
     * @return Query
     */
    abstract public function read($table, $where);

    /**
     * @param $table
     * @param array $values
     * @param $where
     * @return Query
     */
    abstract public function update($table, array $values, $where);

    /**
     * @param $table
     * @param null $where
     * @return Query
     */
    abstract public function delete($table, $where = null);

    protected function log($msg)
    {
        $this->logger->log('DB[' . get_called_class() . ']: ' . $msg);
        return $this;
    }

    abstract public function makeTransaction(\Closure $fn);
}

class RdbmsException extends Exception
{

}
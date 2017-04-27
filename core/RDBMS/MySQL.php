<?php
/**
 * Created by PhpStorm.
 * User: snowgirl
 * Date: 4/25/17
 * Time: 2:35 PM
 */
namespace CORE\RDBMS;

use CORE\RDBMS;
use CORE\RdbmsException;
use CORE\RDBMS\Query\MySQL as Query;

/**
 * Class MySQL
 * @package CORE\RDBMS
 * @property mixed $insert_id
 */
class MySQL extends RDBMS
{
    protected $host;
    protected $port;
    protected $schema;
    protected $user;
    protected $password;
    protected $socket;

    /** @var \mysqli */
    protected $mysqli = null;

    public function __destruct()
    {
        $this->close();
    }

    public function __get($k)
    {
        if ($k == 'found_rows') {
            return (int)$this->query('SELECT FOUND_ROWS() AS ' . $this->quote('c'))->current()['c'];
        } elseif ($k == 'affected_rows') {
            return $this->mysqli->affected_rows;
        } elseif ($k == 'insert_id') {
            return $this->mysqli->insert_id;
        } elseif ($k == 'errno') {
            return substr($this->mysqli->sqlstate, 0, 5);
        } elseif ('errin' == $k) {
            return array(
                $this->mysqli->errno,
                $this->mysqli->error,
            );
        } elseif (property_exists($this->mysqli, $k)) {
            return $this->mysqli->$k;
        }

        return null;
    }

    protected function connect()
    {
        if ($this->mysqli) {
            return;
        }

        $this->mysqli = new \mysqli($this->host, $this->user, $this->password, $this->schema, $this->port, $this->socket);

        if ($this->mysqli->connect_error) {
            $this->close();
            throw new RdbmsException($this->mysqli->connect_error, $this->mysqli->connect_errno);
        }

        $this->mysqli->set_charset('utf8');
    }

    /**
     * @return \mysqli
     */
    public function getMySQLi()
    {
        return $this->mysqli;
    }

    public function close()
    {
        if ($this->mysqli instanceof \mysqli) {
            $this->mysqli->close();
        }

        $this->mysqli = null;
    }

    protected function execute($query, $bind)
    {
        return new Query($this, $query, $bind);
    }

    /**
     * @param $query
     * @param array $bind
     * @return Query|array
     * @throws RdbmsException
     */
    public function query($query, $bind = array())
    {
        $this->connect();
        $this->log($query . ' [' . join(', ', $bind) . ']');
        return $this->execute($query, $bind);
    }

    public function buildSelectSQL($input = '*', $isFoundRows = false, $table = '')
    {
        if (!$input) {
            $input = array('*');
        }

        if (!is_array($input)) {
            $input = array($input);
        }

        $query = array();

        foreach ($input as $v) {
            if ('*' == $v) {
                $query[] = $v;
            } else {
                $query[] = $this->quote($v, $table);
            }
        }

        return 'SELECT ' . ($isFoundRows ? 'SQL_CALC_FOUND_ROWS' : '') . ' ' . join(', ', $query);
    }

    public function buildFromSQL($tables)
    {
        return 'FROM ' . join(', ', array_map(function ($i) {
            return $this->quote($i);
        }, (array)$tables));
    }

    public function buildIndexSQL($index)
    {
        return $index ? ('USE INDEX(' . $this->quote($index) . ')') : '';
    }

    public function buildWhereSQL($where = null, array &$bind, $table = '')
    {
        return ($w = $this->getWhereClause($where, $bind, $table)) ? ('WHERE ' . $w) : '';
    }

    public function buildGroupSQL($group = null, $table = '')
    {
        if ($group) {
            if (!is_array($group)) {
                $group = array($group);
            }

            $query = array();

            foreach ($group as $v) {
                $query[] = $this->quote($v, $table);
            }

            return 'GROUP BY ' . join(', ', $query);
        }
        return '';
    }

    public function buildOrderSQL($order = null, $table = '')
    {
        if ($order) {
            $query = array();

            if (is_array($order)) {
                $map = array(
                    SORT_ASC => 'ASC',
                    SORT_DESC => 'DESC'
                );

                foreach ($order as $k => $v) {
                    $k = $this->quote($k, $table);
                    $query[] = "$k {$map[$v]}";
                }
            } elseif ('random' == $order) {
                $query[] = 'RAND()';
            }

            return 'ORDER BY ' . join(', ', $query);
        }

        return '';
    }

    public function buildLimitSQL(&$limit = null, array &$bind)
    {
        if ($limit) {
            if (is_array($limit)) {
                if (sizeof($limit) > 1) {
                    $from = $limit[0];
                    $to = $limit[1];
                } else {
                    $from = 0;
                    $to = $limit[0];
                }
            } else {
                $from = 0;
                $to = $limit;
            }

            $limit = array($from, $to);
            $bind[] = $from;
            $bind[] = $to;

            return 'LIMIT ?, ?';
        }

        return '';
    }

    protected function getWhereClause($where = null, array &$bind, $table = '')
    {
        if (!$where) {
            return null;
        }

        if (!is_array($where)) {
            $where = array($where);
        }

        $query = array();

        foreach ($where as $k => $v) {
            $k = $this->quote($k, $table);

            if (is_array($v)) {
                if (sizeof($v)) {
                    if (sizeof($v) == 1) {
                        $query[] = $k . ' = ?';
                        $bind[] = $v[0];
                    } else {
                        $query[] = $k . ' IN (' . join(', ', array_fill(0, sizeof($v), '?')) . ')';
                        $bind = array_merge($bind, $v);
                    }
                }
            } elseif (null === $v) {
                $query[] = $k . ' IS NULL';
            } else {
                $query[] = $k . ' = ?';
                $bind[] = $v;
            }
        }

        return join(' AND ', $query);
    }

    public function buildHavingSQL($where = null, array &$bind)
    {
        return ($w = $this->getWhereClause($where, $bind)) ? ('HAVING ' . $w) : '';
    }

    public function quote($v, $table = '')
    {
        if (is_array($v)) {
            foreach ($v as &$k) {
                $k = $this->quote($k, $table);
            }

            return $v;
        }

        if ($table) {
            $table = "`$table`.";
        }

        if ('*' != $v) {
            $v = "`$v`";
        }

        return $table . $v;
    }

    /**
     * @param $table
     * @param array $values
     * @return int
     * @throws RdbmsException
     */
    public function create($table, array $values)
    {
        $bind = $setC = $setV = array();

        foreach ($values as $k => $v) {
            $setC[] = $this->quote($k);
            $setV[] = '?';
            $bind[] = $v;
        }

        $sql = array();
        $sql[] = 'INSERT' . ' INTO ' . $this->quote($table);
        $sql[] = '(' . join(', ', $setC) . ')';
        $sql[] = 'VALUES';
        $sql[] = '(' . join(', ', $setV) . ')';
        $sql = join(' ', $sql);

        $query = $this->query($sql, $bind);

        $id = $query->insert_id;
        $affectedRows = $query->affected_rows;

        return $id ?: $affectedRows;
    }

    public function read($table, $where)
    {
        $bind = array();

        return $this->query(join(' ', array(
            $this->buildSelectSQL('*', false, $bind),
            $this->buildFromSQL($table),
            $this->buildWhereSQL($where, $bind)
        )), $bind);
    }

    public function update($table, array $values, $where)
    {
        $bind = array();
        $set = array();

        foreach ($values as $k => $v) {
            $set[] = $this->quote($k) . ' = ?';
            $bind[] = $v;
        }

        $sql = array();
        $sql[] = 'UPDATE ' . $this->quote($table);
        $sql[] = 'SET ' . join(', ', $set);
        $sql[] = $this->buildWhereSQL($where, $bind);
        $sql = join(' ', $sql);

        return $this->query($sql, $bind)
            ->affected_rows;
    }

    public function delete($table, $where = null)
    {
        $bind = array();
        $sql = array();
        $sql[] = 'DELETE' . ' FROM ' . $this->quote($table);
        $sql[] = $this->buildWhereSQL($where, $bind);
        $sql = join(' ', $sql);

        return $this->query($sql, $bind)->affected_rows;
    }

    protected $isTransactionOpened;

    public function isTransactionOpened()
    {
        return $this->isTransactionOpened;
    }

    public function openTransaction()
    {
        $this->log(__FUNCTION__);

        if ($this->isTransactionOpened()) {
            $this->log('Previous transaction is not closed');
        }

        $this->connect();
        $this->isTransactionOpened = true;
        $this->mysqli->autocommit(false);

        return $this;
    }

    public function commitTransaction()
    {
        $this->log(__FUNCTION__);
        $this->connect();
        $this->isTransactionOpened = false;
        $this->mysqli->commit();
        $this->mysqli->autocommit(true);

        return $this;
    }

    public function rollBackTransaction()
    {
        $this->log(__FUNCTION__);
        $this->connect();
        $this->isTransactionOpened = false;
        $this->mysqli->rollback();
        $this->mysqli->autocommit(true);

        return $this;
    }

    public function makeTransaction(\Closure $fn, $default = null)
    {
        $this->openTransaction();
        try {
            $output = $fn($this);
            $this->commitTransaction();
        } catch (\Exception $ex) {
            $this->rollBackTransaction();

            //@todo check if not database ex - than throw it
//            throw $ex;
            $output = $default;
        }

        return $output;
    }
}
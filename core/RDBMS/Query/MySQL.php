<?php
/**
 * Created by PhpStorm.
 * User: snowgirl
 * Date: 4/25/17
 * Time: 3:41 PM
 */
namespace CORE\RDBMS\Query;

use CORE\RDBMS;
use CORE\RDBMS\Query;
use CORE\RDBMS\MySQL as Provider;
use CORE\Exception;

/**
 * Class Query
 * @package CORE\RDBMS
 * @property int $affected_rows
 * @property int $num_rows
 * @property int $errno
 * @property array $errin
 * @property Provider $provider
 */
class MySQL extends Query
{
    /**
     * @var \mysqli_result|bool
     */
    protected $result;

    protected $pointer = 0;

    /**
     * @var \mysqli_stmt
     */
    protected $stmt;

    public function execute()
    {
        $this->stmt = $this->provider->getMySQLi()->stmt_init();

        if (!$this->stmt->prepare($this->query)) {
//            var_dump($this->stmt->error);die;
            throw new Exception($this->stmt->error, $this->stmt->errno);
        }

        if ($this->bind) {
            array_unshift($this->bind, str_repeat('s', sizeof($this->bind)));
            $tmp = array();

            foreach ($this->bind as $k => &$value) {
                $tmp[$k] = &$value;
            }

            if (!call_user_func_array(array($this->stmt, 'bind_param'), $tmp)) {
                throw new Exception($this->stmt->error, $this->stmt->errno);
            }
        }

        if (!$this->stmt->execute()) {
            throw new Exception($this->stmt->error, $this->stmt->errno);
        }

        $this->result = $this->stmt->get_result();

        if ($this->stmt->error) {
            throw new Exception($this->stmt->error, $this->stmt->errno);
        }
    }

    public function __destruct()
    {
        if (is_object($this->result)) {
            $this->result->free();
        }

        if ($this->stmt && !$this->stmt->close()) {
            throw new Exception($this->stmt->error, $this->stmt->errno);
        }
    }

    /**
     * @param null $key
     * @return array|bool
     */
    public function getArray($key = null)
    {
        $return = array();
        $this->result->data_seek(0);

        if ($key) {
            while ($row = $this->get()) {
                $return[$row[$key]] = $row;
            }
        } else {
            while ($row = $this->get()) {
                $return[] = $row;
            }
        }

        $this->result->data_seek($this->pointer);
        return $return;
    }

    public function rewind()
    {
        $this->pointer = 0;
    }

    public function current()
    {
        if (!$this->valid()) {
            return null;
        }

        $this->result->data_seek($this->pointer);
        return $this->get();
    }

    public function key()
    {
        return $this->pointer;
    }

    public function next()
    {
        ++$this->pointer;
    }

    public function valid()
    {
        return $this->pointer < $this->result->num_rows;
    }

    public function count()
    {
        return $this->result->num_rows;
    }

    public function seek($position)
    {
        $this->pointer = (int)$position;
    }

    public function offsetExists($offset)
    {
        return $this->pointer < $this->result->num_rows;
    }

    public function offsetGet($offset)
    {
        $this->pointer = (int)$offset;
        return $this->current();
    }

    public function offsetSet($offset, $value)
    {
    }

    public function offsetUnset($offset)
    {
    }

    protected function get()
    {
        return $this->result->fetch_array(MYSQLI_ASSOC);
    }
}
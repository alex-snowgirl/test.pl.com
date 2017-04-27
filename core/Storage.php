<?php
/**
 * Created by PhpStorm.
 * User: snowgirl
 * Date: 4/14/17
 * Time: 5:42 PM
 */
namespace CORE;

/**
 * Simple storage class
 *
 * Interface Storage
 * @package CORE
 */
interface Storage
{
    public function insert($entity, $data);

    public function get($entity, $key);

    public function getByFilter($entity, array $filter = array());

    public function update($entity, $key, $data);

    public function delete($entity, $key);
}
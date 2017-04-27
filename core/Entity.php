<?php
/**
 * Created by PhpStorm.
 * User: snowgirl
 * Date: 4/26/17
 * Time: 1:27 PM
 */
namespace CORE;

use APP\Entity\Product;

/**
 * !!! Simple Entity class
 * !!! Simple Entity Manager Class
 * @todo split...
 *
 * Class Entity
 * @package CORE
 * @property integer $id
 */
abstract class Entity
{
    public function __construct(array $attr = array())
    {
        foreach ($attr as $k => $v) {
            $this->$k = $v;
        }
    }

    /**
     * Factory method - from request
     *
     * @param Request $request
     * @return Entity
     */
    public static function makeFromRequest(Request $request)
    {
        $entity = new static;

        foreach ($entity as $k => $v) {
            if (isset($request->$k)) {
                $entity->$k = $request->$k;
            }
        }

//        $table = self::makeTable($entity);
//
//        if (isset($request->$table)) {
//            $entity->id = $request->$table;
//        }

        return $entity;
    }

    protected static function makeTable(Entity $entity)
    {
        return strtolower(join('_', explode('\\', str_replace('APP\\Entity\\', '', get_class($entity)))));
    }

    protected static function makeArray(Entity $entity, $skipKeys = false, $skipValues = false)
    {
        $output = (array)$entity;

        if (false !== $skipKeys) {
            if (!is_array($skipKeys)) {
                $skipKeys = array($skipKeys);
            }

            $output = array_filter($output, function ($key) use ($skipKeys) {
                return !in_array($key, $skipKeys);
            }, ARRAY_FILTER_USE_KEY);
        }

        if (false !== $skipValues) {
            if (!is_array($skipValues)) {
                $skipValues = array($skipValues);
            }

            $output = array_filter($output, function ($value) use ($skipValues) {
                return !in_array($value, $skipValues);
            });
        }

        return $output;
    }

    /**
     * @param Entity[] $entities
     * @param string $key
     * @return Entity[]
     */
    public static function mapAsKeyToItem($entities, $key = 'id')
    {
        $ids = array();

        foreach ($entities as $entity) {
            $ids[] = $entity->$key;
        }

        $output = array_combine($ids, $entities);

        return $output;
    }

    protected static function makeItems(array $items)
    {
        $output = array();

        foreach ($items as $item) {
            $entity = new static;

            foreach ($item as $k => $v) {
                $entity->$k = $v;
            }

            $output[] = $entity;
        }

        return $output;
    }

    public static function create(Entity $entity, RDBMS $rdbms)
    {
        return $rdbms->create(self::makeTable($entity), self::makeArray($entity, 'id'));
    }

    /**
     * @param Entity $entity
     * @param RDBMS $rdbms
     * @return Entity|Entity[]
     */
    public static function read(Entity $entity, RDBMS $rdbms)
    {
        return $entity::makeItems($rdbms->read(self::makeTable($entity), self::makeArray($entity, false, null))->getArray());
    }

    public static function update(Entity $entity, RDBMS $rdbms)
    {
        return $rdbms->update(self::makeTable($entity), self::makeArray($entity, 'id'), array('id' => $entity->id));
    }

    public static function delete(Entity $entity, RDBMS $rdbms)
    {
        return $rdbms->delete(self::makeTable($entity), array('id' => $entity->id));
    }
}
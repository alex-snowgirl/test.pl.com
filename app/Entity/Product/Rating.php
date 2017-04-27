<?php
/**
 * Created by PhpStorm.
 * User: snowgirl
 * Date: 4/26/17
 * Time: 1:31 PM
 */
namespace APP\Entity\Product;

use APP\Entity\Product;
use CORE\Entity;
use CORE\RDBMS;

/**
 * Class Rating
 * @package APP\Entity\Product
 */
class Rating extends Entity
{
    public $product_id;
    public $user_id;
    public $mark;

    /**
     * @todo check marks...
     * @todo validation...
     * @todo check consistency...
     *
     * @param Entity $rating
     * @param RDBMS $rdbms
     * @return mixed
     */
    public static function create(Entity $rating, RDBMS $rdbms)
    {
        /** @var Rating $rating */
        return $rdbms->makeTransaction(function (RDBMS $rdbms) use ($rating) {
            $id = parent::create($rating, $rdbms);

            //update product rating
            /** @var Product $product */
            $product = Entity::read(new Product(array('id' => $rating->product_id)), $rdbms)[0];

            $product->rating += $rating->mark;
            $product->vote_count += 1;

            Product::update($product, $rdbms);

            return $id;
        });
    }
}
<?php
/**
 * Created by PhpStorm.
 * User: snowgirl
 * Date: 4/26/17
 * Time: 1:26 PM
 */
namespace APP\Entity;

use CORE\Entity;
use CORE\RDBMS;
use APP\Entity\Product\Rating;

/**
 * Class Product
 * @package APP\Entity
 * @property mixed $user_mark
 */
class Product extends Entity
{
    public $id;
    public $name;
    public $price;
    public $image;

    public $rating;
    public $vote_count;

    /**
     * !!! Simple Products fetcher with User rating
     * @todo implement single request (LEFT JOIN)
     *
     * @param Product $product
     * @param User $user
     * @param RDBMS $rdbms
     * @return Product[]
     */
    public static function readAddUserRating(Product $product, User $user, RDBMS $rdbms)
    {
        /** @var Product[] $products */
        $products = parent::read($product, $rdbms);
        $products = static::mapAsKeyToItem($products);

        $rating = new Rating(array('user_id' => $user->id));
        $userRatings = Rating::read($rating, $rdbms);
        $userRatings = static::mapAsKeyToItem($userRatings, 'product_id');

        foreach ($products as $product) {
            if (isset($userRatings[$product->id])) {
                $product->user_mark = $userRatings[$product->id]->mark;
            } else {
                $product->user_mark = null;
            }
        }

        return $products;
    }
}
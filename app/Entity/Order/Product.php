<?php
/**
 * Created by PhpStorm.
 * User: snowgirl
 * Date: 4/26/17
 * Time: 1:34 PM
 */
namespace APP\Entity\Order;

use CORE\Entity;

/**
 * Class Product
 * @package APP\Entity\Order
 */
class Product extends Entity
{
    public $order_id;
    public $product_id;
    public $quantity;
}
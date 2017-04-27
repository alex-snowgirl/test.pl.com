<?php
/**
 * Created by PhpStorm.
 * User: snowgirl
 * Date: 4/26/17
 * Time: 1:32 PM
 */
namespace APP\Entity;

use CORE\Entity;
use CORE\RDBMS;

use APP\Entity\Order\Product as OrderProduct;

/**
 * !!! Simple Order Entity
 *
 * Class Order
 * @package APP\Entity
 */
class Order extends Entity
{
    public $id;
    public $user_id;
    public $delivery_id;

    /**
     * @todo validation...
     * @todo check consistency...
     *
     * @param Order $order
     * @param $productIdToQuantity
     * @param RDBMS $rdbms
     * @return mixed
     */
    public static function createCustom(Order $order, $productIdToQuantity, RDBMS $rdbms)
    {
        return $rdbms->makeTransaction(function (RDBMS $rdbms) use ($order, $productIdToQuantity) {
            //create order record
            $id = static::create($order, $rdbms);

            //create order_product records
            foreach ($productIdToQuantity as $productId => $quantity) {
                $orderProduct = new OrderProduct();
                $orderProduct->order_id = $id;
                $orderProduct->product_id = $productId;
                $orderProduct->quantity = $quantity;

                OrderProduct::create($orderProduct, $rdbms);
            }

            //update user balance
            /** @var Product[] $products */
            $products = Entity::read(new Product(array('id' => array_keys($productIdToQuantity))), $rdbms);

            $total = 0;

            foreach ($products as $product) {
                $total += $product->price * $productIdToQuantity[$product->id];
            }

            /** @var Delivery $delivery */
            $delivery = Entity::read(new Delivery(array('id' => $order->delivery_id)), $rdbms)[0];

            $total += $delivery->price;

            /** @var User $user */
            $user = Entity::read(new User(array('id' => $order->user_id)), $rdbms)[0];

            $user->balance -= $total;

            User::update($user, $rdbms);

            return $id;
        });
    }
}
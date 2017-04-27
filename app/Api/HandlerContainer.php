<?php
/**
 * Created by PhpStorm.
 * User: snowgirl
 * Date: 4/26/17
 * Time: 12:25 AM
 */
namespace APP\Api;

use APP\Entity\Delivery;
use APP\Entity\Product;
use APP\Entity\User;
use APP\Entity\Order;
use APP\Entity\Product\Rating;

use CORE\App;

use CORE\Entity;
use CORE\Request;
use CORE\Web\Request as WebRequest;

/**
 * Class HandlerContainer
 * @package APP\Api
 */
class HandlerContainer extends \CORE\HandlerContainer
{
    public function bindCustomHandlers(Request $request)
    {
        /** @var WebRequest $request */

        $request->bind('post', 'user', array($this, 'createUser'));

        /**
         *  {id}, {user_id} here - is for common purposes
         *  anyway App should have internal state
         *  security layer will check stored client id and received
         */

//        $request->bind('patch', 'user/{id}', array($this, 'updateUser'));

        $request->bind('get', 'user/{id}', array($this, 'getUser'));

        $request->bind('get', 'products-with-rating/{user_id}', array($this, 'getProductsWithRatings'));

        $request->bind('get', 'products', array($this, 'getProducts'));

        $request->bind('get', 'deliveries', array($this, 'getDeliveries'));

        $request->bind('post',
            'order/{user_id}/delivery/{delivery_id}',
            array($this, 'createOrder')
        );

        $request->bind('post',
            'rating/product/{product_id}/user/{user_id}/mark/{mark}',
            array($this, 'createProductRating')
        );
    }

    public function bindDefaultHandler(Request $request)
    {
        /** @var WebRequest $request */

        $request->bind('', '', function (App $app) {
            $app->response->setCode(404)->setBody('Not Found');
        });
    }

    /**
     * @todo validate request...
     * @todo error handlers
     * @param App $app
     */
    public function getUser(App $app)
    {
        $user = User::makeFromRequest($app->request);

        if ($user = Entity::read($user, $app->rdbms)) {
            $app->response->setCode(200)->setBody($user[0]);
        }
    }

    /**
     * @todo validate request...
     * @todo error handlers
     * @param App $app
     */
    public function createUser(App $app)
    {
        $user = User::makeFromRequest($app->request);

        if ($id = Entity::create($user, $app->rdbms)) {
            $app->response->addHeader('Location: user/' . $id)
                ->setCode(201)->setBody(Entity::read(new User(array(
                    'id' => $id
                )), $app->rdbms)[0]);
        }
    }

    /**
     * @todo validate request...
     * @todo error handlers
     * @param App $app
     */
    public function updateUser(App $app)
    {
        $user = User::makeFromRequest($app->request);

        if (Entity::update($user, $app->rdbms)) {
            $app->response->setCode(200)->setBody('OK');
        }
    }

    /**
     * @todo validate request...
     * @todo error handlers
     * @param App $app
     */
    public function getProducts(App $app)
    {
        $product = Product::makeFromRequest($app->request);

        $products = Product::read($product, $app->rdbms);
        $products = Product::mapAsKeyToItem($products);

        $app->response->setCode(200)->setBody($products);
    }

    /**
     * @todo validate request...
     * @todo error handlers
     * @param App $app
     */
    public function getProductsWithRatings(App $app)
    {
        /** @var Product $product */
        $product = Product::makeFromRequest($app->request);

        $user = new User(array('id' => $app->request->user_id));

        $products = Product::readAddUserRating($product, $user, $app->rdbms);
        $products = Product::mapAsKeyToItem($products);

        $app->response->setCode(200)->setBody($products);
    }

    /**
     * @todo validate request...
     * @todo error handlers
     * @param App $app
     */
    public function getDeliveries(App $app)
    {
        $delivery = Delivery::makeFromRequest($app->request);

        $deliveries = Delivery::read($delivery, $app->rdbms);
        $deliveries = Delivery::mapAsKeyToItem($deliveries);

        $app->response->setCode(200)->setBody($deliveries);
    }

    /**
     * @todo validate request...
     * @todo error handlers
     * @param App $app
     */
    public function createOrder(App $app)
    {
        /** @var Order $order */
        $order = Order::makeFromRequest($app->request);

        $productIdToQuantity = $app->request->product_id_to_quantity;

        if ($id = Order::createCustom($order, $productIdToQuantity, $app->rdbms)) {
            $app->response->addHeader('Location: order/' . $id)
                ->setCode(201)->setBody(array(
                    'user' => Entity::read(new User(array(
                        'id' => $order->user_id
                    )), $app->rdbms)[0],
                    'order' => Entity::read(new Order(array(
                        'id' => $id
                    )), $app->rdbms)[0]
                ));
        }
    }

    /**
     * @todo validate request...
     * @todo error handlers
     * @param App $app
     */
    public function createProductRating(App $app)
    {
        /** @var Rating $rating */
        $rating = Rating::makeFromRequest($app->request);

        if (Rating::create($rating, $app->rdbms)) {
            $app->response->addHeader('Location: product-rating/' . $rating->product_id . '/' . $rating->user_id)
                ->setCode(201)->setBody(array(
                    'product' => Entity::read(new Product(array(
                        'id' => $rating->product_id
                    )), $app->rdbms)[0],
                    'product-rating' => Entity::read(new Rating(array(
                        'product_id' => $rating->product_id,
                        'user_id' => $rating->user_id
                    )), $app->rdbms)[0]
                ));
        }
    }
}
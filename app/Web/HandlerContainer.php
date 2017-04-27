<?php
/**
 * Created by PhpStorm.
 * User: snowgirl
 * Date: 4/25/17
 * Time: 11:49 PM
 */
namespace APP\Web;

use CORE\App;

use CORE\Request;
use CORE\Web\Request as WebRequest;

/**
 * Class HandlerContainer
 * @package APP\Web
 */
class HandlerContainer extends \CORE\HandlerContainer
{
    public function bindCustomHandlers(Request $request)
    {
        /** @var WebRequest $request */

        $request->bind('get', '/', array($this, 'index'));
    }

    public function bindDefaultHandler(Request $request)
    {
        /** @var WebRequest $request */

        $request->bind('', '', function (App $app) {
            $app->response->setCode(404)
                ->setBody('Not Found');
        });
    }

    public function index(App $app)
    {
        $tmp = time();

        $config = json_encode(array(
            'apiEndpoint' => 'api.php',
            'defaultBalance' => $app->config->raw->shop->{'balance.default'},
            'imagesWebPath' => $app->config->raw->web->{'images.path'},
            'ratingStarsCount' => $app->config->raw->shop->{'rating.stars'},
            'isCacheProducts' => $app->config->raw->shop->{'cache.products'},
            'isCacheDeliveries' => $app->config->raw->shop->{'cache.deliveries'},
        ));

        /**
         * !!! Simple template engine
         * @todo View object and templates...
         */
        $output = <<<HTML
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Shop</title>
    <link rel="stylesheet" href="media/app.css?_=$tmp">
</head>
<body>
    <h1>The Shop</h1>
    <div id="app" class="loading"></div>
    <script type="text/javascript" src="media/jquery.min.js"></script>
    <script type="text/javascript" src="media/app.js?_=$tmp"></script>
    <script type="text/javascript">new shopApp('app', $config);</script>
</body>
</html>
HTML;

        $app->response->setCode(200)
            ->setBody($output);
    }
}
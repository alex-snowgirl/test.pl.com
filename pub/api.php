<?php
/**
 * Created by PhpStorm.
 * User: snowgirl
 * Date: 4/14/17
 * Time: 7:41 PM
 */
use CORE\App;
use CORE\Config;

use CORE\Web\Request;
use CORE\Web\View\JSON;
use CORE\Web\Response;
use APP\Api\HandlerContainer;

require_once '../ini.php';
$loader = require_once '../vendor/autoload.php';

$_ROOT = __DIR__;

/**
 * !!! Simple application with wide control (mounted components)
 */
$app = new App(
    new Config($loader, $_ROOT . '/../config.ini'),
    new Request(),
    new HandlerContainer(),
    new Response(new JSON())
);

$app->on(App::EVENT_EXCEPTION, function (App $app, Exception $ex) {
    $app->response->setCode(500);

    if ('dev' == $app->config->raw->app->env) {
        $app->response->setBody($ex->getTraceAsString());
    } else {
        $app->response->setBody('Ooops! Something bad happened over here...');
    }

    $app->response->send();
});

$app->run();
<?php
/**
 * Created by PhpStorm.
 * User: snowgirl
 * Date: 4/13/17
 * Time: 5:13 PM
 */
use CORE\App;
use CORE\Config;

use CORE\Web\Request;
use CORE\Web\View\HTML;
use CORE\Web\Response;
use APP\Web\HandlerContainer;
use CORE\Exception;

require_once '../ini.php';
$loader = require_once '../vendor/autoload.php';

/**
 * !!! Simple application with wide control (mounted components)
 */
$app = new App(
    new Config($loader, __DIR__ . '/../config.ini'),
    new Request(),
    new HandlerContainer(),
    new Response(new HTML())
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
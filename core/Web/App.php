<?php
/**
 * Created by PhpStorm.
 * User: snowgirl
 * Date: 4/14/17
 * Time: 7:42 PM
 */
namespace CORE\Web;

use CORE\Response;
use CORE\Storage;
use CORE\View;
use CORE\Web\View as WebView;
use CORE\Web\Request as WebRequest;
use CORE\Web\Response as WebResponse;
use CORE\Response\Exception as ResponseException;
use CORE\Exception as CoreException;

/**
 * Simple Web Application class
 *
 * Class App
 * @package CORE\Web
 */
class App extends \CORE\App
{
    /**
     * @return App
     */
    protected function run()
    {
        /** @var WebRequest $request */
        /** @var WebResponse $response */

        try {
            /**
             * Actually this class is not needed, coz we have single-page app
             * We could put all required html into web.php
             * But made this for visual representing, so...
             *
             * Controller init should be here
             * Than Action Call should return some output
             *
             * But, taking into account the fact of single-page app...
             */

            $tmp = time();

            $output = <<<HTML
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Game</title>
    <link rel="stylesheet" href="media/app.css?_=$tmp">
</head>
<body>
    <h1>The Game</h1>
    <div id="app" class="loading"></div>
    <script type="text/javascript" src="media/jquery.min.js"></script>
    <script type="text/javascript" src="media/app.js?_=$tmp"></script>
    <script type="text/javascript">new gameApp('app', 'api.php');</script>
</body>
</html>
HTML;

            $this->response->setCode(200)
                ->setBody($output);
        } catch (ResponseException $ex) {
            $this->response->setCode($ex->getCode())
                ->setBody($ex->getMessage());
        } catch (CoreException $ex) {
            $this->response->setCode(500)
                ->setBody('Our API-Server is gone away! Sorry!');
        } catch (\Exception $ex) {
            $this->response->setCode(500)
                ->setBody('Ooops! Sorry!');
        }

        return $this;
    }

    protected function iniRequest()
    {
        return new WebRequest();
    }

    protected function iniResponse(View $view)
    {
        /** @var WebView $view */
        return new WebResponse($view);
    }
}
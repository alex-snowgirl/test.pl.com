<?php
/**
 * Created by PhpStorm.
 * User: snowgirl
 * Date: 4/13/17
 * Time: 7:33 PM
 */
namespace CORE\Web\View;

use CORE\Web\Response;
use CORE\Web\View;

/**
 * Class JSON
 * @package CORE\Web\View
 */
class JSON extends View
{
    public function prepare(Response $response)
    {
        $response->addHeader('Content-Type: application/json');
        return $this->generate(array(
            'code' => $response->getCode(),
            'body' => $response->getBody()
        ));
    }

    public function generate($param)
    {
        return json_encode((array)$param);
    }
}
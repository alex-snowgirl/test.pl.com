<?php
/**
 * Created by PhpStorm.
 * User: snowgirl
 * Date: 4/13/17
 * Time: 5:24 PM
 */
namespace CORE;

/**
 * !!! Simple Application for handling all types of requests and responses
 *
 * Class App
 * @package CORE
 * @property Request|\CORE\Web\Request $request
 * @property HandlerContainer|\APP\Web\HandlerContainer $handlerContainer
 * @property Response|\CORE\Web\Response $response
 * @property RDBMS $rdbms
 * @property Logger $logger
 */
class App extends Observable
{
    const EVENT_EXCEPTION = 0;
    const EVENT_PRE_RUN = 1;
    const EVENT_POST_RUN = 2;

    /**
     * Public for simple access interface @todo...
     * @var Config
     */
    public $config;
    /**
     * Public for simple access interface @todo...
     * @var Request
     */
    public $request;
    /**
     * Public for simple access interface @todo...
     * @var HandlerContainer
     */
    public $handlerContainer;
    /**
     * Public for simple access interface @todo...
     * @var Response
     */
    public $response;

    public function __construct(Config $config, Request $request, HandlerContainer $handlerContainer, Response $response)
    {
        $this->config = $config;
        $this->request = $request;
        $this->handlerContainer = $handlerContainer;
        $this->response = $response;
    }

    public function __get($k)
    {
        return $this->config->$k;
    }

    protected function executeHandler($handler)
    {
        if ($handler instanceof \Closure) {
            $handler($this);
        } elseif (is_callable($handler, true)) {
            call_user_func($handler, $this);
        } else {
            //@todo...
        }
    }

    public function run()
    {
        $this->trigger(self::EVENT_PRE_RUN);
        try {
            $this->handlerContainer->bindHandlers($this->request);
            $handler = $this->request->parse();
            $this->executeHandler($handler);
            $this->response->send();
        } catch (Exception $ex) {
            $this->trigger(self::EVENT_EXCEPTION, $ex);
        }

        $this->trigger(self::EVENT_POST_RUN);
    }
}
<?php
/**
 * Created by PhpStorm.
 * User: snowgirl
 * Date: 4/25/17
 * Time: 11:47 PM
 */
namespace CORE;

/**
 * Interface HandlerContainer
 * @package CORE
 */
abstract class HandlerContainer
{
    public function bindHandlers(Request $request)
    {
        $this->bindCustomHandlers($request);
        $this->bindDefaultHandler($request);
    }

    abstract public function bindCustomHandlers(Request $request);

    abstract public function bindDefaultHandler(Request $request);
}
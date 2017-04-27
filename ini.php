<?php
/**
 * Created by PhpStorm.
 * User: snowgirl
 * Date: 4/13/17
 * Time: 7:29 PM
 */

date_default_timezone_set('Europe/Kiev');
ini_set('error_log', __DIR__ . '/app.log');

/**
 * Simple logger
 *
 * @param $text
 */
function LogMe($text)
{
    error_log($text . "\n", 3, ini_get('error_log'));
}

/**
 * Simple error handler
 * E_ERROR & E_WARNING generate Exceptions
 */
set_error_handler(function ($num, $str, $file, $line) {
    if (in_array($num, array(E_ERROR, E_WARNING))) {
        throw new Exception($str, $num);
    }

    LogMe(join(' ', array(
        '[error_handler]',
        '[' . $num . '] ' . $str,
        '[' . $line . '] ' . $file,
    )));

    return true;
});

/**
 * Simple exception handler
 */
set_exception_handler(function (\Exception $ex) {
    LogMe(join(' ', array(
        '[exception_handler]',
        '[' . $ex->getCode() . '] ' . $ex->getMessage(),
        "\n",
        $ex->getTraceAsString()
    )));
});

/**
 * Simple shutdown handler
 * @todo output something
 */
register_shutdown_function(function () {
    if (!$e = error_get_last()) {
        return true;
    }

    LogMe(join(' ', array(
        '[shutdown_handler]',
        '[' . $e['type'] . '] ' . $e['message'],
        '[' . $e['line'] . '] ' . $e['file'],
    )));

    return true;
});
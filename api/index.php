<?php

/**
 * PNP Integration
 *
 * This module provides extensive analytics on a platform of choice
 * Currently support Google Analytics and Piwik
 *
 * @package     local_pnp
 * @category    upgrade
 * @copyright   2020 Kelson Medeiros <kelsoncm@gmail.com>
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_pnp;

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

function exception_handler($exception)
{
    /*
        200 – 208, 226, 
        300 – 305, 307, 308
        400 – 417, 422 – 424, 426, 428 – 429, 431
        500 – 508, 510 – 511
    */
    $error_code = $exception->getCode() ?: 500;
    http_response_code($error_code);
    die(json_encode(["error" => ["message" => $exception->getMessage(), "code" => $error_code]]));
}


class service
{

    function authenticate()
    {
        $auth_token = \get_config('local_pnp', 'auth_token');

        $headers = getallheaders();
        $authentication_key = array_key_exists('Authentication', $headers) ? "Authentication" : "authentication";
        if (!array_key_exists($authentication_key, $headers)) {
            throw new \Exception("Bad Request - Authentication not informed", 400);
        }

        if ("Token $auth_token" != $headers[$authentication_key]) {
            throw new \Exception("Unauthorized", 401);
        }
    }

    function call()
    {
        $this->authenticate();
        echo json_encode($this->do_call()) . "\n";
    }

    function do_call()
    {
        throw new \Exception("Não implementado", 501);
    }
}


try {
    require_once(\dirname(\dirname(\dirname(__DIR__))) . '/config.php');
    header('Content-Type: application/json; charset=utf-8');
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);

    set_exception_handler('\local_pnp\exception_handler');

    $whitelist = [
        'get_certificates',
    ];
    $params = explode('&', $_SERVER["QUERY_STRING"]);
    $service_name = $params[0];

    if ((!in_array($service_name, $whitelist))) {
        throw new \Exception("Serviço não existe", 404);
    }
    require_once "$service_name.php";

    $service_class = "\local_pnp\\$service_name" . "_service";
    $service = new $service_class();
    $service->call();
} catch (\Exception $e) {
    exception_handler($e);
}

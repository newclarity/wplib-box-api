<?php

namespace PressBoxx\Api;
use Slim\App;
use Slim\Http\Request;
use Slim\Http\Response;

/**
 * Class Route
 * @package PressBoxx\Api
 *
 * @property string $route
 * @property array  $methods
 */
class Route {

    /**
     * @var string
     */
    private $_route;

    /**
     * @var array
     */
    private $_methods;

    /**
     * Route constructor.
     * @param string $uri
     * @param array  $args
     */
    function __construct($uri, $args){

        $this->_route = $uri;
        $this->_methods  = (array)$args['methods'];

    }

    /**
     * @param  $name
     * @return string
     */
    function __get($name){

        $value = null;

        if (property_exists($this, "_{$name}")) {
            $property = "_{$name}";
            $value = $this->$property;
        }

        return $value;

    }

    function discover_route($baseUrl) {

        $route = [];
        foreach ($this->_methods as $key => $method ) {
            $route['methods'][]   = strtoupper($key);
            $route['endpoints'][] = [
                'methods' => [$key],
                'args'    => []
            ];
        }

        $route['links'] = [ "self" => sprintf( 'http://%1$s/%2$s', $baseUrl, $this->_route)];

        return $route;

    }

    function add_method($method) {
        array_merge($this->_methods, $method);
    }

    /**
     * @param string $namespace
     * @param App    $app
     */
    function register($namespace, $app) {
        foreach ( $this->_methods as $method => $params ) {
            $endpoint = sprintf('/%1$s/%2$s', $namespace, $this->_route);
            $message  = $params['message'];
            $command  = $params['command'];

            $app->$method("{$endpoint}", function(Request $request, Response $response, $args)
            use ( $message, $command ) {
                /**
                 * @var \Slim\App $this
                 */
                $this->logger->info($message . implode(', ', $args));

                return $this->api->cli()->process_command($command, $response, $args);

            });
        }
    }
}
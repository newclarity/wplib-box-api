<?php

namespace PressBoxx;

use PressBoxx\Api\ApiNamespace;
use PressBoxx\Api\Route;
use Slim\App;
use WPLIB_Box\WPLIB_Box_CLI_Interface;

/**
 * Class API
 * @package PressBoxx
 */
class API {

    /**
     * @var App
     */
    private $_app;

    /**
     * @var Route
     */
    private $_default_route;

    /**
     * @var string
     */
    private $_base_url;

    /**
     * @var ApiNamespace[]
     */
    private $_namespaces = [];

    /**
     * @var WPLIB_Box_CLI_Interface
     */
    private $_cliInterface;

    /**
     * API constructor.
     *
     * @param \Slim\App $app
     * @param array $args
     */
    function __construct($app, $args = array()){

        $this->_app = $app;

        if (! isset($args['cli'])) {
            $args['cli'] = new WPLIB_Box_CLI_Interface();
        }

        if ( ! isset($args['base_url'])) {
            $args['base_url'] = filter_input(INPUT_SERVER, 'HTTP_HOST', FILTER_SANITIZE_URL);
        }

        $this->_cliInterface  = $args['cli'];
        $this->_base_url      = $args['base_url'];
        $this->_default_route = new Route( '/', ['endpoint' => '/', 'methods' => ['get' => [],]]);

        if (file_exists(__DIR__ . '/api.json')) {
            $this->_namespaces = json_decode(file_get_contents(__DIR__ . '/api.json'), true)['namespaces'];
        }

        array_walk($this->_namespaces, function(&$params, $version){
            $params =  new ApiNamespace($version, $params);
        });

    }

    function namespaces() {

        return $this->_namespaces;

    }

    function version($namespace){

        return $this->namespaces()[$namespace];

    }

    static function url() {

        return sprintf(
            '%1$s://%2$s',
            1 == filter_input(INPUT_SERVER, 'HTTPS', FILTER_VALIDATE_BOOLEAN) ? 'https' : 'http',
            filter_input(INPUT_SERVER, 'HTTP_HOST', FILTER_SANITIZE_URL )
        );

    }

    function register() {
        $app        = $this->_app;
        $namespaces = $this->namespaces();
        array_walk($namespaces, function(ApiNamespace $namespace) use($app){
            $namespace->register($app);
        });
    }

    function discover() {

        return [
            'name'        => 'PressBoxx API',
            'url'         => 'http://' . filter_input(INPUT_SERVER, 'HTTP_HOST', FILTER_SANITIZE_URL),
            'description' => 'Resources for interacting with the PressBoxx infrastructure',
            'namespaces'  => array_keys($this->namespaces()),
            'routes'      => $this->_routes(),
        ];

    }

    function cli() {

        return $this->_cliInterface;

    }

    private function _routes() {
        $routes = [];

        $routes['/'] = $this->_default_route->discover_route($this->_base_url);

        foreach ($this->namespaces() as $namespace ) {
            foreach ($namespace->routes() as $route) {
                $routes[ $namespace->name . '/' . $route->endpoint ] = $route->discover_route($this->_base_url . '/' . $namespace->name);
            }
        }

        return $routes;
    }

    private function make_url($resource) {

        return sprintf('http://%1$s/%2$s', $this->_base_url, $resource);

    }

}

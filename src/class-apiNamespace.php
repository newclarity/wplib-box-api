<?php

namespace PressBoxx\Api;

use PressBoxx\API;

/**
 * Class ApiNamespace
 * @package PressBoxx\Api
 * @property string $name
 */
class ApiNamespace {

    /**
     * @var string
     */
    private $_name;

    /**
     * @var string
     */
    private $_endpoint;

    /**
     * @var Route[]
     */
    private $_routes = array();

    function __construct($name, $args = array()){

        if (! isset($args['actions'])) {
            $args['actions'] = [];
        }

        $this->_name  = $name;
        $this->_endpoint = sprintf( '%1$s/%2$s', API::url(), $name);
        $this->_routes   = $args['routes'];

        array_walk($this->_routes, function(&$params, $action){
            $params = new Route($action, $params);
        });
    }

    /**
     * @param \Slim\App $app
     */
    function register($app){
        array_walk($this->_routes, function($route) use($app){
            /**
             * @var Route $route
             */
            $route->register($this->_name, $app);
        });
    }

    function routes() {

        return $this->_routes;

    }

    /**
     * @param  $name
     * @return null|mixed
     */
    function __get($name){

        $value = null;

        if (property_exists($this, "_{$name}")) {
            $property = "_{$name}";
            $value    = $this->$property;
        }

        return $value;

    }

}

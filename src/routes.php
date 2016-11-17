<?php
// Routes

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\App;

//$app->get('/[{name}]', function ($request, $response, $args) {
//    // Sample log message
//    $this->logger->info("Slim-Skeleton '/' route");
//
//    // Render index view
//    return $this->renderer->render($response, 'index.phtml', $args);
//});

if (file_exists(__DIR__ . '/api.json')) {
    $api = json_decode(file_get_contents(__DIR__ . '/api.json'), true);
    foreach ($api as $version => $params) {
        register_version($app, $version, $params);
    }
}

function register_version($app, $version, $params) {
    foreach ($params['commands'] as $command) {
        $command['endpoint'] = sprintf('/%1$s%2$s', $version, $command['endpoint']);
        register_methods($app, $command);
    }
}

/**
 * @param App   $app
 * @param array $command
 */
function register_methods($app, $command) {
    foreach ($command['methods'] as $method => $params) {
        register_method($app, $command['endpoint'], $method, $params);
    }
}

/**
 * @param App    $app
 * @param string $route
 * @param string $method
 * @param array  $params
 */
function register_method($app, $route, $method, $params) {
    $app->$method("{$route}", function (Request $request, Response $response, $args)
    use ($params) {
        $args = array_merge($args, parse_params($params['parameters']));

        $this->logger->info($params['message'] . implode(', ', $args));

        if (verify_params($params['parameters'], $args)) {
            $results = $this->cli->process_command($params['cliCommand'], $response, $args);
        }
        return $results;
    });
}

/**
 * @param  array $params
 * @return array
 */
function parse_params($params) {

    parse_str(file_get_contents('php://input'), $data);

    foreach($params as $key => $param) {
        if (empty($data[$key])) {
            $data[$key] = $param['default'];
        }
    }

    return $data;
}

/**
 * Verify that all required parameters are present in the args.
 *
 * @param  array $params
 * @param  array $args
 * @return bool
 */
function verify_params($params, $args) {
    $return = true;

    foreach($params as $key => $param) {
        if (true == $param['required'] && ! array_key_exists($key, $args)) {
            $return = false;
        }
    }

    return $return;
}

$app->get('/v1', function(Request $request, Response $response, $args) {

    $version = $this->api->version('v1');
    $actions = $this->api->version('v1')->discovery();

    return json_encode(array_map(function($action) use($version){
        return [
            'endpoint' => $version->endpoint . $action->endpoint,
            'method'   => $action->method,
        ];
    }, $actions));

});
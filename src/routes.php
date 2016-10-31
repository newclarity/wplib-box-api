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

        $this->logger->info($params['message'] . implode(', ', $args));

        return $this->cli->process_command($params['cliCommand'], $response, $args);

    });
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
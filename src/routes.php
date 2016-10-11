<?php
// Routes

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

//$app->get('/[{name}]', function ($request, $response, $args) {
//    // Sample log message
//    $this->logger->info("Slim-Skeleton '/' route");
//
//    // Render index view
//    return $this->renderer->render($response, 'index.phtml', $args);
//});

$app->get('/', function(Request $request, Response $response, $args) {
    return json_encode($this->api->discover());
});

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

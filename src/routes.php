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

$commands = [];

if (file_exists(__DIR__ . '/routes.json')) {
    $commands = json_decode(file_get_contents(__DIR__ . '/routes.json'));
}

foreach( $commands as $route => $params ) {
    $message = $params->message;
    $command = $params->command;
    $method  = $params->method;

    $app->$method("/v1/{$route}", function(Request $request, Response $response, $args)
    use ( $message, $command ) {

        $this->logger->info($message . implode(', ', $args));

        return $this->cli->process_command($command, $response, $args);

    });

}


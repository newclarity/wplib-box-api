<?php
// Routes

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

$app->get('/[{name}]', function ($request, $response, $args) {
    // Sample log message
    $this->logger->info("Slim-Skeleton '/' route");

    // Render index view
    return $this->renderer->render($response, 'index.phtml', $args);
});

$app->group('/box', function() {
    $this->put('/processvm/{pvm}', function(Request $request, Response $response, $args) {

        $this->logger->info( "Switch process VM to {$args['pvm']}");

        $command = "set-cli-processvm-{$args['pvm']}";

        return $this->cli->process_command( $command, $response );

    });

});

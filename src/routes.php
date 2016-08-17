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

        $response = $response->withJson(['message' => 'Command not found', 'command' => $command], 500);

        // add check for existent command
        if (file_exists("/vagrant/scripts/guest/cli/commands/{$command}")) {
            ob_start();
            passthru("box {$command}");
            $message = ob_get_clean();

            $response = $response->withJson(['message' => $message, 'command' => $command], 200);
        }

        return $response;
    });

});

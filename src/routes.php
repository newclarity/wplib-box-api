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

    // Cache group
    $this->group('/cache', function() {

        $this->put('/flush', function(Request $request, Response $response) {

            $this->logger->info("Flush caches");

            $command = "cache-flush";

            return $this->cli->process_command($command, $response);

        });

    });

    // Database group
    $this->group('/database/', function() {

        $this->post('backup/[{db_name}]', function(Request $request, Response $response, $args) {

            $this->logger->info(sprintf('Backing up database%1$s', ! empty($args['db_name']) ? " {$args['db_name']}" : 'default' ) );

            $command = 'backup-db';

            return $this->cli->process_command( $command, $response );

        });

        $this->post('import/{db_file}', function(Request $request, Response $response, $args) {

            $this->logger->info("Importing {$args['db_file']}");

            $command = "import-db";

            return $this->cli->process_command($command, $response, $args);

        });

    });

    });

    $this->put('/processvm/{pvm}', function(Request $request, Response $response, $args) {

        $this->logger->info( "Switch process VM to {$args['pvm']}");

        $command = "set-cli-processvm-{$args['pvm']}";

        return $this->cli->process_command($command, $response);

    });

});

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
    $this->group('/database', function() {

        $this->post('/backup', function(Request $request, Response $response) {

            $command = 'backup-db';

            return $this->cli->process_command( $command, $response );

        });

        $this->post('/import/{db_file}', function(Request $request, Response $response, $args) {

            $this->logger->info("Importing {$args['db_file']}");

            $command = "import-db";

            return $this->cli->process_command($command, $response, $args);

        });

    });

    // File watchers group
    $this->group('/file-watchers', function() {

        $this->put('/enable', function(Request $request, Response $response) {

            $this->logger->info('Enable file watchers');

            $command = "enable-file-watchers";

            return $this->cli->process_command($command, $response);

        });

    });

    // Object cache group
    $this->group('/object-cache', function() {

        $this->put('/enable', function(Request $request, Response $response, $args) {

            $this->logger->info("Enabling object cache for {$args['siteId']}");

            $command = "enable-object-caching";

            return $this->cli->process_command($command, $response);

        });

        $this->put('/disable', function(Request $request, Response $response, $args) {

            $this->logger->info("Disabling object cache for {$args['siteId']}");

            $command = "disable-object-caching";

            return $this->cli->process_command($command, $response);

        });

    });

    // Plugins group
    $this->group('/plugins/{plugin}', function() {

        $this->put('/install', function(Request $request, Response $response, $args) {

            $this->logger->info("Installing plugin {$args['plugin']}");

            $command = "download-plugin";

            return $this->cli->process_command($command, $response, $args);

        });

    });

    $this->put('/processvm/{pvm}', function(Request $request, Response $response, $args) {

        $this->logger->info("Switch process VM to {$args['pvm']}");

        $command = "set-processvm-{$args['pvm']}";

        return $this->cli->process_command($command, $response);

    });

});

// Site endpoints
$app->group('/sites', function() {

    // Get a list of all sites
    $this->get('', function(Request $request, Response $response) {

        $body = $response->getBody();
        $body->write("List of all sites");

        return $response->withBody($body);

    });

    //create a site
    $this->post('', function(Request $request, Response $response, $args) {

        $body = $response->getBody();
        $body->write("Create a new site");

        return $response->withBody($body);

    });

    $this->group('/{siteId:[0-9]+}', function() {

        // Get a site's information
        $this->get('', function(Request $request, Response $response, $args) {

            $body = $response->getBody();
            $body->write("Site {$args['siteId']} information");

            return $response->withBody($body);

        });

        // Update a site
        $this->put('', function(Request $request, Response $response, $args) {

            $body = $response->getBody();
            $body->write("Update site {$args['siteId']}");

            return $response->withBody($body);

        });

        // Delete a site
        $this->delete('', function(Request $request, Response $response, $args) {

            $body = $response->getBody();
            $body->write("Delete site {$args['siteId']}");

            return $response->withBody($body);

        });

    });

});

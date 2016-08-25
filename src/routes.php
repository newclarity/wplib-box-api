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
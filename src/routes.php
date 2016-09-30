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

if (file_exists(__DIR__ . '/api.json')) {
    $api = json_decode(file_get_contents(__DIR__ . '/api.json'), true);
    foreach ($api as $version) {
        foreach ($version['commands'] as $command) {
            $endpoint   = $command['endpoint'];
            $message    = $command['message'];
            $cliCommand = $command['cliCommand'];
            $method     = $command['method'];

            $app->$method("{$endpoint}", function(Request $request, Response $response, $args)
            use ( $message, $cliCommand ) {

                $this->logger->info($message . implode(', ', $args));

                return $this->cli->process_command($cliCommand, $response, $args);

            });

        }
    }

}

$app->get('/api', function(Request $request, Response $response, $args) use($app) {
    $api = json_decode(file_get_contents(__DIR__ . '/api.json'), true);

    array_walk($api, function(&$version) {
        $rootUrl = $version['root_url'];
        array_walk($version['commands'], function(&$command) use($rootUrl) {
            $command['endpoint'] = $rootUrl . $command['endpoint'];
        });
    });

    return json_encode($api);
});

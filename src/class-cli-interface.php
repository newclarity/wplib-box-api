<?php
namespace WPLIB_Box;

use Psr\Http\Message\ResponseInterface as Response;

class WPLIB_Box_CLI_Interface {

    /**
     * @param  string   $command
     * @param  Response $response
     * @param  array    $args
     * @return Response
     */
    function process_command($command, Response $response, $args =[])
    {
        $response = $response->withJson(['message' => 'Not implemented', 'command' => $command], 503);

        // add check for existent command
        if (file_exists("/vagrant/scripts/guest/cli/commands/{$command}")) {
            $status = 500;

            foreach ($args as $arg) {
                $command .= ' ' . $arg;
            }

            exec("box {$command}", $message, $exitCode);

            if(0 === $exitCode) {
                $status = 200;
            }

            $response = $response->withJson(['message' => $message, 'command' => $command], $status);
        }

        return $response;

    }

}

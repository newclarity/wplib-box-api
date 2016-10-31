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
        $response = $response->withJson(['status' => 'error', 'data' => [$command => 'Not implemented']], 503);

        // add check for existent command
        if (file_exists("/boxx/cli/commands/{$command}")) {
            $status = 500;

            foreach ($args as $arg) {
                $command .= ' ' . $arg;
            }

            exec("/boxx/cli/box {$command} --quiet", $message, $exitCode);

            if(0 === $exitCode) {
                $status = 200;
            }

            $response = $response->withJson(['status' => 'success', 'data' => json_decode($this->process_response($message))], $status);
        }

        return $response;

    }

    /**
     * This command will take an array of text ouput lines and format into a JSON string
     * @param array $response
     *
     * @return string
     */
    function process_response($response) {
        $map =  array_map(function($line) {
            return trim($line);
        }, $response);

        return implode('',$map);
    }

}

<?php

namespace App\Controllers;

use Symfony\Component\HttpFoundation\Response;

class BaseController
{
    /**
     * @param $data
     * @return Response
     */
    public function respondWithSuccess($data): Response
    {
        $response = new Response();
        $response->setContent(json_encode(
            $data
        ));
        $response->setStatusCode(Response::HTTP_OK);
        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }

    /**
     * @param string $message
     * @return Response
     */
    public function respondWithNotFound(string $message = ""): Response
    {
        $response = new Response();
        $response->setContent(json_encode(
            [
                "message" => $message
            ]
        ));
        $response->setStatusCode(Response::HTTP_NOT_FOUND);
        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }

    /**
     * @param string $message
     * @return Response
     */
    public function respondWithError(string $message = ""): Response
    {
        $response = new Response();
        $response->setContent(json_encode([
            'status' => "error",
            'message' => $message
        ]));
        $response->setStatusCode(Response::HTTP_INTERNAL_SERVER_ERROR);
        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }
}
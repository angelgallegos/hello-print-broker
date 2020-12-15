<?php

namespace App\Controllers;

use Symfony\Component\HttpFoundation\Request as HttpRequest;
use App\Entities\Request;
use App\Services\RequestService;
use Symfony\Component\HttpFoundation\Response;

class RequestController extends BaseController
{
    /**
     * @var RequestService|null
     */
    public ?RequestService $requestService = null;

    /**
     * RequestController constructor.
     * @param RequestService|null $requestService
     */
    public function __construct(?RequestService $requestService)
    {
        $this->requestService = $requestService;
    }

    /**
     * @param string $token
     * @return Request|object|null
     */
    public function get(string $token)
    {
        $request = $this->requestService->getByToken($token);

        if (!$request) {
            return $this->respondWithNotFound("The Request couldn't be located");
        }

        return $this->respondWithSuccess($request);
    }

    /**
     * @param HttpRequest $httpRequest
     * @return mixed
     */
    public function create(HttpRequest $httpRequest): Response
    {
        $data = $httpRequest->toArray();
        $request = $this->requestService->create($data);

        if ($request)
            return $this->respondWithSuccess($request);

        return $this->respondWithError("An error occurred while saving the Request");
    }

    /**
     * @param HttpRequest $httpRequest
     * @return Response
     */
    public function update(HttpRequest $httpRequest)
    {
        $data = $httpRequest->toArray();
        $request = $this->requestService->getByToken($data["token"]);

        if (!$request) {
            return $this->respondWithNotFound("The Request couldn't be located");
        }

        $request = $this->requestService->update($request, $data);
        if ($request)
            return $this->respondWithSuccess($request);

        return $this->respondWithError("An error occurred while updating the Request");
    }
}
<?php

namespace Framework\Http;

use Exception;
use FastRoute;
use FastRoute\Dispatcher;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpFoundation\Response;

class Handler
{
    /**
     * @var Dispatcher|null
     */
    private ?Dispatcher $dispatcher;

    /**
     * @var ContainerBuilder|null
     */
    private ?ContainerBuilder $container;

    /**
     * Handler constructor.
     * @param ContainerBuilder|null $container
     */
    public function __construct(?ContainerBuilder $container)
    {
        $this->container = $container;

        $this->dispatcher = FastRoute\simpleDispatcher(function (FastRoute\RouteCollector $routes) {
            $routes->addRoute('POST', '/request/create', 'App\Controllers\RequestController@create');
            $routes->addRoute('PUT', '/request/update', 'App\Controllers\RequestController@update');
            $routes->addRoute('GET', '/request/get/{token}', 'App\Controllers\RequestController@get');
        });
    }

    /**
     * @param Request $request
     * @return Response
     * @throws Exception
     */
    public function handle(Request $request): Response
    {
        $routeInfo = $this->dispatcher->dispatch($request->getMethod(), $request->getPathInfo());

        $response = new Response();
        switch ($routeInfo[0]) {
            case FastRoute\Dispatcher::NOT_FOUND:
                $response = new Response(
                    '',
                    Response::HTTP_NOT_FOUND,
                    ['content-type' => 'application/json']
                );
                break;
            case FastRoute\Dispatcher::METHOD_NOT_ALLOWED:
                $allowedMethods = $routeInfo[1];
                $response = new Response(
                    '',
                    Response::HTTP_METHOD_NOT_ALLOWED,
                    ['content-type' => 'application/json']
                );
                break;
            case FastRoute\Dispatcher::FOUND:
                $handler = $routeInfo[1];
                list($class, $method) = explode("@", $handler, 2);
                $controller = $this->container->get($class);
                $vars = $routeInfo[2];
                if (!empty($vars))
                    $response = call_user_func_array([$controller, $method], $vars);
                else
                    $response = call_user_func_array([$controller, $method], [$request]);
                break;
        }

        return $response;
    }
}
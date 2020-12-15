<?php

namespace App\Services;

use App\Entities\Request;
use App\Producers\KafkaProducer;
use App\Repositories\RequestRepository;
use __\__;
use Exception;
use Monolog\Logger;

class RequestService
{
    /**
     * @var RequestRepository
     */
    protected RequestRepository $repository;

    /**
     * @var KafkaProducer
     */
    protected KafkaProducer $producer;

    /**
     * @var Logger
     */
    protected Logger $logger;

    /**
     * RequestService constructor.
     * @param RequestRepository $repository
     * @param KafkaProducer $producer
     * @param Logger $logger
     */
    public function __construct(
        RequestRepository $repository,
        KafkaProducer $producer,
        Logger $logger
    ) {
        $this->repository = $repository;
        $this->producer = $producer;
        $this->logger = $logger;
    }

    /**
     * @param int $id
     * @return Request|object|null
     */
    public function get(int $id)
    {
        return $this->repository->get($id);
    }

    /**
     * @param string $token
     * @return Request|object|null
     */
    public function getByToken(string $token)
    {
        return $this->repository->getByToken($token);
    }

    /**
     * @param array $data
     * @return Request|null
     */
    public function create(array $data): ?Request
    {
        $request = new Request();
        $request->setMessage(__::get($data, "message"));
        $request->setStatus("created");
        try {
            $request->setToken(bin2hex(random_bytes(16)));
        } catch (Exception $exception) {
            $this->logger->alert("The service encountered an error while creating the token");
            return null;
        }

        $request = $this->repository->save($request);
        if ($request) {
            if (!$this->producer->send($request, 'topic_a'))
                return null;

            return $request;
        }

        return null;
    }

    /**
     * @param Request $request
     * @param array $data
     * @return Request|null
     */
    public function update(
        Request $request,
        array $data
    ): ?Request {
        $request->setMessage(__::get($data, "message"));
        $request->setStatus(__::get($data, "status"));

        //If the request has been marked as farewell do not send
        //to Kafka
        if ($request->getStatus() !== "farewell") {
            $this->producer->send($request, 'topic_b');
        }

        return $this->repository->save($request);
    }
}
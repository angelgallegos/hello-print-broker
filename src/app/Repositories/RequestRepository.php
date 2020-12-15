<?php

namespace App\Repositories;

use App\Entities\Request;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\ORM\TransactionRequiredException;

class RequestRepository
{
    private EntityManager $entityManager;

    /**
     * RequestRepository constructor.
     * @param EntityManager $entityManager
     */
    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @param int $id
     * @return Request|object|null
     */
    public function get(int $id): ?Request
    {
        try {
            return $this->entityManager->find(Request::class, $id);
        } catch (OptimisticLockException $e) {
        } catch (TransactionRequiredException $e) {
        } catch (ORMException $e) {
        } finally {
            return null;
        }
    }

    /**
     * @param string $token
     * @return Request|null
     */
    public function getByToken(string $token): ?Request
    {
        try {
            return $this->entityManager->createQueryBuilder()
                ->select("r")
                ->from(Request::class, "r")
                ->where('r.token = :token')
                ->setParameter('token', $token)
                ->getQuery()
                ->getSingleResult();
        } catch (NoResultException | NonUniqueResultException $e) {
            return null;
        }
    }

    /**
     * @param Request $request
     * @return Request|null
     */
    public function save(Request $request): ?Request
    {
        try {
            $this->entityManager->persist($request);
            $this->entityManager->flush();

            return $request;
        } catch (ORMException | OptimisticLockException | ORMException $e) {
            return null;
        }
    }
}
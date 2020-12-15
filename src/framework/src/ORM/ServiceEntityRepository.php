<?php

namespace Framework\ORM;

use Doctrine\ORM\EntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use LogicException;

class ServiceEntityRepository extends EntityRepository
{
    /**
     * @param ManagerRegistry $registry
     * @param string $entityClass The class name of the entity this repository manages
     */
    public function __construct(ManagerRegistry $registry, string $entityClass)
    {
        $manager = $registry->getManagerForClass($entityClass);

        if ($manager === null) {
            throw new LogicException(sprintf(
                'Could not find the entity manager for class "%s". Check your Doctrine configuration to make sure it is configured to load this entity’s metadata.',
                $entityClass
            ));
        }

        parent::__construct($manager, $manager->getClassMetadata($entityClass));
    }
}
<?php

namespace Infrastructure\Database\Doctrine;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;

abstract class Repository extends EntityRepository implements RepositoryInterface
{
    /**
     * @param EntityManager $entityManager
     */
    public function __construct(EntityManager $entityManager)
    {
        $class = $entityManager->getClassMetadata(get_class($this->getModel()));

        parent::__construct($entityManager, $class);
    }
}

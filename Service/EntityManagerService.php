<?php

namespace Lincode\RestApi\Bundle\Service;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping\Entity;

class EntityManagerService
{
    private $em;
    private $repository;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    public function setRepository($repository)
    {
        $this->repository = $this->em->getRepository($repository);
    }

    public function findOneBy($filters = [])
    {
        return $this->repository->findOneBy($filters);
    }

    public function findBy($filters = [], $order_by = [], $limit = null, $offset = null)
    {
        return $this->repository->findBy($filters, $order_by, $limit, $offset);
    }

    public function save($entity)
    {
        $this->em->persist($entity);
        $this->em->flush();
    }

    public function remove($entity)
    {
        $this->em->remove($entity);
        $this->em->flush();
    }
}
<?php

namespace App\Repository;

trait RepositoryUtils
{
    public function save(object $entity): object
    {
        $em = $this->getEntityManager();
        $em->persist($entity);
        $em->flush();

        return $entity;
    }

    public function delete(object $entity): void
    {
        $em = $this->getEntityManager();
        $em->remove($entity);
        $em->flush();
    }
}

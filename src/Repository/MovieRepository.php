<?php

namespace App\Repository;

use App\Entity\Movie;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;

/**
 * @method Movie|null find($id, $lockMode = null, $lockVersion = null)
 * @method Movie|null findOneBy(array $criteria, array $orderBy = null)
 * @method Movie[]    findAll()
 * @method Movie[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MovieRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Movie::class);
    }

    /**
     * @param $movies Movie[]
     *
     * @return void
     */
    public function saveBatch(array $movies): void
    {
        $em = $this->getEntityManager();

        foreach ($movies as $movie) {
            $existing = $this->findOneByImdbId($movie->getImdbId());

            if ($existing) {
                continue;
            }

            $em->persist($movie);
        }

        $em->flush();
    }
}

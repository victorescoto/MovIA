<?php

namespace App\Repository;

use App\Entity\Rating;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Rating|null find($id, $lockMode = null, $lockVersion = null)
 * @method Rating|null findOneBy(array $criteria, array $orderBy = null)
 * @method Rating[]    findAll()
 * @method Rating[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RatingRepository extends ServiceEntityRepository
{
    use RepositoryUtils;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Rating::class);
    }

    public function getTopRated(int $limit = 5): array
    {
        return array_slice($this->getEntityManager()->createQuery(
            'SELECT IDENTITY(r.movie) as movie_id, avg(r.rate) as rate_avg '
                . 'FROM App\Entity\Rating r '
                . 'GROUP BY r.movie '
                . 'ORDER BY rate_avg '
        )->getResult(), 0, $limit);
    }
}

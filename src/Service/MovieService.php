<?php

namespace App\Service;

use App\Repository\MovieRepository;

class MovieService
{
    private $repository;

    public function __construct(MovieRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @param $movies App\Entity\Movie[]
     *
     * @return App\Entity\Movie[]
     */
    public function saveMovies(array $movies)
    {
        $this->repository->saveBatch($movies);
    }
}

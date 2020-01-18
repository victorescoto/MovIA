<?php

namespace App\Service;

use App\Entity\Movie;
use App\Exception\IMDBException;
use App\Http\Request;
use App\Repository\IMDBRepository;

class IMDBService implements ApiServiceInterface
{
    private $repository;

    public function __construct(IMDBRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @return App\Entity\Movie[]
     */
    public function searchMovies(string $title): array
    {
        $seachResult = $this->repository->searchMovies($title);

        if ($seachResult->hasErrors()) {
            throw new IMDBException($seachResult->getError());
        }

        $movies = [];

        foreach ($seachResult->getResults() as $movieInfo) {
            $movies[] = (new Movie)
                ->setTitle($movieInfo['Title'])
                ->setYear($movieInfo['Year'])
                ->setImdbId($movieInfo['imdbID'])
                ->setType($movieInfo['Type'])
                ->setPoster($movieInfo['Poster']);
        }

        return $movies;
    }
}

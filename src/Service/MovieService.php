<?php

namespace App\Service;

use App\Entity\Movie;
use App\Http\Request;
use App\Repository\MovieRepository;

class MovieService
{
    protected $repository;

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

    public function getMovies(): array
    {
        return $this->repository->findAll();
    }

    /**
     * @param $movies App\Entity\Movie[]
     *
     * @return App\Entity\Movie[]
     */
    public function getMovie(int $id): Movie
    {
        return $this->repository->find($id);
    }

    public function createMovie(Request $request): Movie
    {
        $movie = (new Movie)
            ->setTitle($request->json->get('title'))
            ->setYear($request->json->get('year'))
            ->setImdbId($request->json->get('imdbId'))
            ->setType($request->json->get('type'))
            ->setPoster($request->json->get('poster'));

        return $this->repository->save($movie);
    }

    public function updateMovie(Request $request, Movie $movie): Movie
    {
        $movie
            ->setTitle($request->json->get('title'))
            ->setYear($request->json->get('year'))
            ->setImdbId($request->json->get('imdbId'))
            ->setType($request->json->get('type'))
            ->setPoster($request->json->get('poster'));

        return $this->repository->save($movie);
    }

    public function deleteMovie(Movie $movie): void
    {
        $this->repository->delete($movie);
    }
}

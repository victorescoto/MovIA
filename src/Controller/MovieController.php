<?php

namespace App\Controller;

use App\Entity\Movie;
use App\Http\Request;
use App\Service\MovieService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class MovieController extends AbstractController
{
    protected $movieService;

    public function __construct(MovieService $movieService)
    {
        $this->movieService = $movieService;
    }

    /**
     * @Route("/api/movies", methods={"GET"})
     */
    public function list()
    {
        $movies = $this->movieService->getMovies();
        return $this->json($movies, 200, [], ['groups' => 'movie.list']);
    }

    /**
     * @Route("/api/movies", methods={"POST"})
     */
    public function create(Request $request)
    {
        $movie = $this->movieService->createMovie($request);
        return $this->json($movie, 201, [], ['groups' => 'movie.detail']);
    }

    /**
     * @Route("/api/movies/{id}", methods={"GET"}, requirements={"id":"\d+"})
     */
    public function show(Movie $movie)
    {
        return $this->json($movie, 200, [], ['groups' => 'movie.detail']);
    }

    /**
     * @Route("/api/movies/{id}", methods={"PUT"}, requirements={"id":"\d+"})
     */
    public function update(Request $request, Movie $movie)
    {
        $movie = $this->movieService->updateMovie($request, $movie);
        return $this->json($movie, 200, [], ['groups' => 'movie.detail']);
    }

    /**
     * @Route("/api/movies/{id}", methods={"DELETE"}, requirements={"id":"\d+"})
     */
    public function delete(Movie $movie)
    {
        $this->movieService->deleteMovie($movie);
        return $this->json($movie, 200, [], ['groups' => 'movie.detail']);
    }
}

<?php

namespace App\Controller;

use App\Entity\Movie;
use App\Entity\Rating;
use App\Entity\User;
use App\Http\Request;
use App\Service\RatingService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class RatingController extends AbstractController
{
    protected $ratingService;

    public function __construct(RatingService $ratingService)
    {
        $this->ratingService = $ratingService;
    }

    /**
     * @Route("/api/ratings", methods={"GET"})
     */
    public function list()
    {
        $ratings = $this->ratingService->getRatings();
        return $this->json($ratings);
    }

    /**
     * @Route("/api/ratings", methods={"POST"})
     */
    public function create(Request $request)
    {
        $rating = $this->ratingService->createRating($request);
        return $this->json($rating, 201);
    }

    /**
     * @Route("/api/ratings/{id}", methods={"GET"})
     */
    public function show(Rating $rating)
    {
        return $this->json($rating);
    }

    /**
     * @Route("/api/ratings/{id}", methods={"PUT"})
     */
    public function update(Request $request, Rating $rating)
    {
        $rating = $this->ratingService->updateRating($request, $rating);
        return $this->json($rating);
    }

    /**
     * @Route("/api/ratings/{id}", methods={"DELETE"})
     */
    public function delete(Rating $rating)
    {
        $this->ratingService->deleteRating($rating);
        return $this->json($rating);
    }

    /**
     * @Route("/api/movies/{id}/ratings", methods={"GET"})
     */
    public function listByMovie(Movie $movie)
    {
        $ratings = $this->ratingService->getRatingsByMovie($movie);
        return $this->json($ratings);
    }

    /**
     * @Route("/api/users/{id}/ratings", methods={"GET"})
     */
    public function listByUser(User $user)
    {
        $ratings = $this->ratingService->getRatingsByUser($user);
        return $this->json($ratings);
    }
}

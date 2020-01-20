<?php

namespace App\Controller;

use App\Entity\Movie;
use App\Entity\Rating;
use App\Entity\User;
use App\Http\Request;
use App\Service\RabbitMQService;
use App\Service\RatingService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class RatingController extends AbstractController
{
    protected $ratingService;
    protected $rabbitMQService;

    public function __construct(RatingService $ratingService, RabbitMQService $rabbitMQService)
    {
        $this->ratingService = $ratingService;
        $this->rabbitMQService = $rabbitMQService;
    }

    /**
     * @Route("/api/ratings", methods={"GET"})
     */
    public function list()
    {
        $ratings = $this->ratingService->getRatings();
        return $this->json($ratings, 200, [], [
            'groups' => [
                'rating.list',
                'user.list',
                'movie.list',
            ]
        ]);
    }

    /**
     * @Route("/api/ratings", methods={"POST"})
     */
    public function create(Request $request)
    {
        $rating = $this->ratingService->createRating($request);
        return $this->json($rating, 201, [], [
            'groups' => [
                'rating.list',
                'user.list',
                'movie.list',
            ]
        ]);
    }

    /**
     * @Route("/api/ratings/{id}", methods={"GET"}, requirements={"id":"\d+"})
     */
    public function show(Rating $rating)
    {
        return $this->json($rating, 200, [], [
            'groups' => [
                'rating.list',
                'user.list',
                'movie.list',
            ]
        ]);
    }

    /**
     * @Route("/api/ratings/{id}", methods={"PUT"}, requirements={"id":"\d+"})
     */
    public function update(Request $request, Rating $rating)
    {
        $rating = $this->ratingService->updateRating($request, $rating);
        return $this->json($rating, 200, [], [
            'groups' => [
                'rating.list',
                'user.list',
                'movie.list',
            ]
        ]);
    }

    /**
     * @Route("/api/ratings/{id}", methods={"DELETE"}, requirements={"id":"\d+"})
     */
    public function delete(Rating $rating)
    {
        $this->ratingService->deleteRating($rating);
        return $this->json($rating, 200, [], [
            'groups' => [
                'rating.list',
                'user.list',
                'movie.list',
            ]
        ]);
    }

    /**
     * @Route("/api/movies/{id}/ratings", methods={"GET"}, requirements={"id":"\d+"})
     */
    public function listByMovie(Movie $movie)
    {
        $ratings = $this->ratingService->getRatingsByMovie($movie);
        return $this->json($ratings, 200, [], [
            'groups' => [
                'rating.list',
                'user.list',
            ]
        ]);
    }

    /**
     * @Route("/api/users/{id}/ratings", methods={"GET"}, requirements={"id":"\d+"})
     */
    public function listByUser(User $user)
    {
        $ratings = $this->ratingService->getRatingsByUser($user);
        return $this->json($ratings, 200, [], [
            'groups' => [
                'rating.list',
                'movie.list',
            ]
        ]);
    }

    /**
     * @Route("/api/ratings/random", methods={"POST"})
     */
    public function randomRatings()
    {
        $this->rabbitMQService->dispatchMessage(['action' => 'generate-ratings']);
        return $this->json(['message' => 'Random ratings are being generated.'], 202);
    }
}

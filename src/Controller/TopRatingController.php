<?php

namespace App\Controller;

use App\Service\MovieService;
use App\Service\RabbitMQService;
use App\Service\RatingService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class TopRatingController extends AbstractController
{
    protected $rabbitMQService;
    protected $ratingService;
    protected $movieService;

    public function __construct(
        RabbitMQService $rabbitMQService,
        RatingService $ratingService,
        MovieService $movieService
    ) {
        $this->rabbitMQService = $rabbitMQService;
        $this->ratingService = $ratingService;
        $this->movieService = $movieService;
    }

    /**
     * @Route("/api/ratings/top", methods="POST")
     */
    public function setTopRating()
    {
        $movies = $this->ratingService->getTopRated();
        $this->rabbitMQService->dispatchMessage(compact('movies'), 'top_rated');

        return $this->json(['message' => 'Top Rated Movies are being selected'], 202);
    }

    /**
     * @Route("/api/ratings/top", methods="GET")
     */
    public function getTopRating()
    {
        $movies = [];

        $this->rabbitMQService->watch(function ($message) use (&$movies) {
            $messageInfo = json_decode($message->body, true);
            $movies = $messageInfo['movies'];

            $this->rabbitMQService->log('top-rated received', 'info', $messageInfo['identifier']);
        }, 'top_rated');

        foreach ($movies as &$movie) {
            $movie = $this->movieService->getMovie($movie['movie_id']);
        }

        return $this->json($movies, 200, [], ['groups' => 'movie.detail']);
    }
}

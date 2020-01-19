<?php

namespace App\Service;

use App\Entity\Movie;
use App\Entity\Rating;
use App\Entity\User;
use App\Http\Request;
use App\Repository\RatingRepository;

class RatingService
{
    protected $repository;
    protected $userService;
    protected $movieService;

    public function __construct(
        RatingRepository $repository,
        UserService $userService,
        MovieService $movieService
    ) {
        $this->repository = $repository;
        $this->userService = $userService;
        $this->movieService = $movieService;
    }

    public function getRatings(): array
    {
        return $this->repository->findAll();
    }

    public function getRatingsByMovie(Movie $movie): array
    {
        return $this->repository->findAllByMovie($movie);
    }

    public function getRatingsByUser(User $user): array
    {
        return $this->repository->findAllByUser($user);
    }

    public function createRating(Request $request): Rating
    {
        $user = $this->userService->getUser($request->json->get('user'));
        $movie = $this->movieService->getMovie($request->json->get('movie'));

        $rating = (new Rating)
            ->setRate($request->json->get('rate'))
            ->setUser($user)
            ->setMovie($movie);

        return $this->repository->save($rating);
    }

    public function updateRating(Request $request, Rating $rating): Rating
    {
        $user = $this->userService->getUser($request->json->get('user'));
        $movie = $this->movieService->getMovie($request->json->get('movie'));

        $rating
            ->setRate($request->json->get('rate'))
            ->setUser($user)
            ->setMovie($movie);

        return $this->repository->save($rating);
    }

    public function deleteRating(Rating $rating): void
    {
        $this->repository->delete($rating);
    }
}

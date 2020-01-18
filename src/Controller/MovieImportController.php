<?php

namespace App\Controller;

use ApiPlatform\Core\Bridge\Symfony\Validator\Exception\ValidationException;
use App\Exception\IMDBException;
use App\Http\Request;
use App\Service\IMDBService;
use App\Service\MovieService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Throwable;

class MovieImportController extends AbstractController
{
    private $imdbService;
    private $movieService;

    public function __construct(IMDBService $imdbService, MovieService $movieService)
    {
        $this->imdbService = $imdbService;
        $this->movieService = $movieService;
    }

    /**
     * @Route("/api/movies/import", methods={"POST"})
     */
    public function import(Request $request, ValidatorInterface $validator)
    {
        try {
            $title = $request->json->get('title');
            $titleConstraints = [
                new Assert\NotNull(['message' => 'The \'title\' should not be null.']),
                new Assert\Length(['min' => 3, 'max' => 100]),
            ];

            $errors = $validator->validate($title, $titleConstraints);
            if (count($errors)) {
                throw new ValidationException($errors);
            }

            $movies = $this->imdbService->searchMovies($title);
            $this->movieService->saveMovies($movies);

            return $this->json(['message' => 'Movies imported successfully']);
        } catch (IMDBException | ValidationException $e) {
            return $this->json(['message' => $e->getMessage()], 400);
        } catch (Throwable $t) {
            return $this->json(['message' => 'Something went wrong, please try again in a moment.'], 500);
        }
    }
}

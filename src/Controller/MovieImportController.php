<?php

namespace App\Controller;

use ApiPlatform\Core\Bridge\Symfony\Validator\Exception\ValidationException;
use App\Http\Request;
use App\Service\RabbitMQService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Throwable;

class MovieImportController extends AbstractController
{
    protected $rabbitMQService;

    public function __construct(RabbitMQService $rabbitMQService)
    {
        $this->rabbitMQService = $rabbitMQService;
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

            $this->rabbitMQService->dispatchMessage([
                'action' => 'import-movies',
                'payload' => compact('title')
            ]);

            return $this->json(['message' => 'Importing movies.'], 202);
        } catch (Throwable $t) {
            return $this->json(['message' => 'Something went wrong, please try again in a moment.'], 500);
        }
    }
}

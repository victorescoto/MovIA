<?php

namespace App\Command;

use ApiPlatform\Core\Bridge\Symfony\Validator\Exception\ValidationException;
use App\Entity\Rating;
use App\Repository\RatingRepository;
use App\Service\IMDBService;
use App\Service\MovieService;
use App\Service\RabbitMQService;
use App\Service\RatingService;
use App\Service\UserService;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Throwable;

class StartQueueConsumerCommand extends Command
{
    protected static $defaultName = 'app:start-queue-consumer';
    protected $imdbService;
    protected $logger;
    protected $movieService;
    protected $rabbitMQService;
    protected $ratingRepository;
    protected $ratingService;
    protected $userService;
    protected $validator;

    public function __construct(
        IMDBService $imdbService,
        LoggerInterface $logger,
        MovieService $movieService,
        RabbitMQService $rabbitMQService,
        RatingRepository $ratingRepository,
        RatingService $ratingService,
        UserService $userService,
        ValidatorInterface $validator
    ) {
        $this->imdbService = $imdbService;
        $this->logger = $logger;
        $this->movieService = $movieService;
        $this->rabbitMQService = $rabbitMQService;
        $this->ratingRepository = $ratingRepository;
        $this->ratingService = $ratingService;
        $this->userService = $userService;
        $this->validator = $validator;

        parent::__construct();
    }

    protected function configure()
    {
        $this->setDescription('Starts the message broker queue consumption service');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $io->success('Queue consumption up and running. Waiting for messages... [To exit press CTRL+C]');

        $this->rabbitMQService->watch(function ($message) use ($io) {
            $messageInfo = json_decode($message->body, true);

            $io->horizontalTable(
                ['message', 'payload'],
                [[$messageInfo['action'], json_encode($messageInfo['payload'] ?? '')]]
            );

            switch ($messageInfo['action'] ?? '') {
                case 'import-movies':
                    $this->importMovies($messageInfo['payload'], $messageInfo['identifier']);
                    break;

                case 'generate-ratings':
                    $this->generateRatings($messageInfo['identifier']);
                    break;

                default:
                    $this->logger->error("{$messageInfo['identifier']} - Invalid Action");
            }
        });

        $io->success('Consumption finished.');

        return 0;
    }

    protected function importMovies(array $data, string $messageId): void
    {
        try {
            $title = $data['title'];

            $titleConstraints = [
                new Assert\NotNull(['message' => 'The \'title\' should not be null.']),
                new Assert\Length(['min' => 3, 'max' => 100]),
            ];

            $errors = $this->validator->validate($title, $titleConstraints);
            if (count($errors)) {
                throw new ValidationException($errors);
            }

            $movies = $this->imdbService->searchMovies($title);
            $this->movieService->saveMovies($movies);

            $this->logger->info("{$messageId} - Movies imported successfully", compact('data', 'messageId'));
        } catch (Throwable $t) {
            $this->logger->error("{$messageId} - {$t->getMessage()}", [
                'messageId' => $messageId,
                'data' => $data,
                'trace' => $t->getTrace(),
            ]);
        }
    }

    protected function generateRatings(string $messageId): void
    {
        $users = $this->userService->getUsers();
        $movies = $this->movieService->getMovies();

        foreach ($users as $user) {
            foreach ($movies as $movie) {
                $rating = $this->ratingService->getRatingByUserAndMovie($user, $movie);

                if (is_null($rating)) {
                    $rating = (new Rating)
                        ->setUser($user)
                        ->setMovie($movie);
                }

                $rating->setRate(rand(1, 5));

                $this->ratingRepository->save($rating);
            }
        }

        $this->logger->info("{$messageId} - Random ratings generated successfully.", compact('messageId'));
    }
}

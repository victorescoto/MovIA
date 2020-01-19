<?php

namespace App\Command;

use ApiPlatform\Core\Bridge\Symfony\Validator\Exception\ValidationException;
use App\Service\IMDBService;
use App\Service\MovieService;
use App\Service\RabbitMQService;
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
    protected $movieService;
    protected $imdbService;
    protected $rabbitMQService;
    protected $validator;
    protected $logger;

    public function __construct(
        MovieService $movieService,
        IMDBService $imdbService,
        RabbitMQService $rabbitMQService,
        ValidatorInterface $validator,
        LoggerInterface $logger
    ) {
        $this->movieService = $movieService;
        $this->imdbService = $imdbService;
        $this->rabbitMQService = $rabbitMQService;
        $this->validator = $validator;
        $this->logger = $logger;

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
                [[$messageInfo['action'], json_encode($messageInfo['payload'])]]
            );

            switch ($messageInfo['action'] ?? '') {
                case 'import-movies':
                    $this->importMovies($messageInfo['payload'], $messageInfo['identifier']);
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
}

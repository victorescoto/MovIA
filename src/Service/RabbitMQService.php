<?php

namespace App\Service;

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

class RabbitMQService extends AMQPStreamConnection
{
    public function connectOnConstruct()
    {
        return false;
    }

    public function dispatchMessage(array $data, string $queue = 'movia'): void
    {
        $this->connect();

        $channel = $this->channel();

        $channel->queue_declare($queue, false, true, false, false);

        $data['identifier'] = md5(date('YmdHis'));
        $message = new AMQPMessage(json_encode($data), [
            'content_type' => 'application/json',
            'delivery_mode' => AMQPMessage::DELIVERY_MODE_PERSISTENT,
        ]);

        $channel->basic_publish($message, '', $queue);

        $channel->close();
        $this->close();
    }

    public function watch(callable $callback, string $queue = 'movia'): void
    {
        $this->connect();

        $channel = $this->channel();

        list(, $messageCount,) = $channel->queue_declare($queue, true);

        if ($messageCount) {
            $channel->basic_consume($queue, '', false, true, false, false, $callback);

            while ($channel->is_consuming()) {
                $channel->wait();
            }
        }

        $channel->close();
        $this->close();
    }

    public function log(string $message, string $logLevel, string $messageId): void
    {
        $this->dispatchMessage([
            'messageId' => $messageId,
            'content' => $message,
            'level' => $logLevel
        ], 'logs');
    }
}

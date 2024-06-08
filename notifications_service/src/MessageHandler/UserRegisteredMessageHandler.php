<?php

namespace App\MessageHandler;

use App\Message\UserRegisteredMessage;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class UserRegisteredMessageHandler
{
    public function __invoke(UserRegisteredMessage $message)
    {
        file_put_contents(__DIR__ . '/../../public/user_registered.txt', $message->getUserId() . PHP_EOL, FILE_APPEND);
    }
}
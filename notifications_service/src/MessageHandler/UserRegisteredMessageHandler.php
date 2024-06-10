<?php

namespace App\MessageHandler;

use App\Message\UserRegisteredMessage;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Path;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class UserRegisteredMessageHandler
{
    public function __invoke(UserRegisteredMessage $message)
    {
        $filesystem = new Filesystem();
        $filesystem->appendToFile('public/user_registered.txt', $message->getUserId() . PHP_EOL);
    }
}
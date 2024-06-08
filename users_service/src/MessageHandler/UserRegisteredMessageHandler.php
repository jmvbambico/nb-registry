<?php
namespace App\MessageHandler;

use App\Message\UserRegisteredMessage;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class UserRegisteredMessageHandler implements MessageHandlerInterface
{
    public function __invoke(UserRegisteredMessage $message)
    {
        return true;
    }
}
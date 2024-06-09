<?php

use PHPUnit\Framework\TestCase;
use App\MessageHandler\UserRegisteredMessageHandler;
use App\Message\UserRegisteredMessage;

class UserRegisteredMessageHandlerTest extends TestCase
{
    private $filePath;

    protected function setUp(): void
    {
        // Define the path to the file where user IDs are stored
        $this->filePath = __DIR__ . '/../../public/user_registered.txt';

        // Ensure a clean state before each test
        if (file_exists($this->filePath)) {
            unlink($this->filePath);
        }
    }

    public function testInvokeCreatesFileAndAppendsUserId()
    {
        // Setup
        $userId = 12345;
        $message = $this->createMock(UserRegisteredMessage::class);
        $message->method('getUserId')->willReturn($userId);
        $handler = new UserRegisteredMessageHandler();

        // Test Invocation
        $handler($message);

        // If the file exists and contains the user ID, it means the handler worked
        $this->assertFileExists($this->filePath);
        $content = file_get_contents($this->filePath);

        // If the file contains the user ID, it means the handler worked
        $this->assertStringContainsString($userId, $content);
    }

    protected function tearDown(): void
    {
        // Teardown: Delete the file after the test
        if (file_exists($this->filePath)) {
            unlink($this->filePath);
        }
    }
}
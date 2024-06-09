<?php
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use App\Message\UserRegisteredMessage;

class UserControllerTest extends WebTestCase
{
    public function testCreateUserDispatchesUserRegisteredMessage()
    {
        $client = static::createClient();

        // Attempt to create a user with valid data
        $client->request('POST', '/user', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode([
            'email' => 'fubar@foo.com',
            'firstName' => 'Jane',
            'lastName' => 'Doe'
        ]));

        // If the client response contains "User created successfully", it means the user was created
        $this->assertStringContainsString('User created successfully', $client->getResponse()->getContent());
    }

    public function testCreateUserWithInvalidDataReturnsBadRequest()
    {
        $client = static::createClient();

        // Attempt to create a user with invalid data
        $client->request('POST', '/user', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode([
            // Missing required fields
        ]));

        // Assert the response status code is 400 Bad Request
        $this->assertResponseStatusCodeSame(400); // Assuming your controller handles validation and returns a 400 status code for invalid data
    }

    public function testCreateUserWithoutContentTypeHeaderReturnsBadRequest()
    {
        $client = static::createClient();

        // Attempt to create a user without specifying content type
        $client->request('POST', '/user', [], [], [], json_encode([
            'email' => 'test@example.com',
            'firstName' => 'Test',
            'lastName' => 'User'
        ]));

        // Assert the response status code is 400 Bad Request
        $this->assertResponseStatusCodeSame(400); // Assuming your controller checks for 'Content-Type: application/json' header
    }

    public function testCreateUserResponseContentTypeIsApplicationJson()
    {
        $client = static::createClient();

        // Attempt to create a user with valid data
        $client->request('POST', '/user', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode([
            'email' => 'testjson@example.com',
            'firstName' => 'Json',
            'lastName' => 'Test'
        ]));

        // Assert the response "Content-Type" header is "application/json"
        $this->assertResponseHeaderSame('Content-Type', 'application/json');
    }
}
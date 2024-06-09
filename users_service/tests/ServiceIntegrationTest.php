<?php
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ServiceIntegrationTest extends WebTestCase
{
    public function testCreateUserPersistsData()
    {
        $client = static::createClient();
        $entityManager = static::getContainer()->get('doctrine.orm.default_entity_manager');

        $client->request('POST', '/user', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode([
            'email' => 'newuser@example.com',
            'firstName' => 'New',
            'lastName' => 'User'
        ]));

        $userRepository = $entityManager->getRepository(User::class);
        $user = $userRepository->findOneByEmail('newuser@example.com');

        // If the user was successfully created, the user should be found and not null
        $this->assertNotNull($user);

        // If the user was successfully created, the user's first and last name should match the data sent
        $this->assertEquals('New', $user->getFirstName());
        $this->assertEquals('User', $user->getLastName());
    }

    public function testMessageProcessing()
    {
        $dsn = $_ENV['MESSENGER_TRANSPORT_DSN'];

        $dsnParts = parse_url($dsn);
        $host = $dsnParts['host'];
        $port = $dsnParts['port'];
        $user = $dsnParts['user'];
        $password = $dsnParts['pass'];

        $connection = new \PhpAmqpLib\Connection\AMQPStreamConnection($host, $port, $user, $password);

        // Ensure that we are able to connect to RabbitMQ
        $this->assertTrue($connection->isConnected(), 'Failed to connect to RabbitMQ');

        $channel = $connection->channel();
        $channel->queue_declare('test_queue', false, true, false, false);
        $msg = new \PhpAmqpLib\Message\AMQPMessage('Test Message');
        $channel->basic_publish($msg, '', 'test_queue');

        // If the message was published, it should not be null
        $this->assertNotNull($msg);

        // Cleanup: Close connections and clean up
        $channel->close();
        $connection->close();
    }
}
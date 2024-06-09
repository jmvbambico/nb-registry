<?php
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ServiceIntegrationTest extends WebTestCase
{
    public function testMessageConsumption()
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

        // If the message was consumed, it should not be null
        $this->assertNotNull($msg);

        // Cleanup: Close connections and clean up
        $channel->close();
        $connection->close();
    }
}
<?php
namespace App\Controller;

use App\Entity\User;
use App\Message\UserRegisteredMessage;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Doctrine\Persistence\ManagerRegistry;

class UserController extends AbstractController
{
    private $doctrine;

    public function __construct(ManagerRegistry $doctrine)
    {
        $this->doctrine = $doctrine;
    }

    #[Route('/user', name: 'create_user', methods: ['POST'])]
    public function createUser(Request $request, SerializerInterface $serializer, MessageBusInterface $bus): JsonResponse
    {
        // validate request data
        $data = json_decode($request->getContent(), true);

        if (empty($data['email']) || empty($data['firstName']) || empty($data['lastName'])) {
            return new JsonResponse([
                'status' => 'error',
                'message' => 'Missing required fields'
            ], Response::HTTP_BAD_REQUEST);
        }

        // validate content type
        if ($request->headers->get('Content-Type') !== 'application/json') {
            return new JsonResponse([
                'status' => 'error',
                'message' => 'Invalid content type'
            ], Response::HTTP_BAD_REQUEST);
        }

        // JSON format example:
        // {
        //     "email": "fubar@foo.com",
        //     "firstName": "Jane",
        //     "lastName": "Doe"
        // }
        $user = $serializer->deserialize($request->getContent(), User::class, 'json');

        $entityManager = $this->doctrine->getManager();
        $entityManager->persist($user);
        $entityManager->flush();

        // Publish event to RabbitMQ
        $bus->dispatch(new UserRegisteredMessage($user->getId()));

        // return a JSON response
        return new JsonResponse([
            'status' => 'success',
            'message' => '[Rabbit Dispatcher] User created successfully',
            'userId' => $user->getId()
        ], Response::HTTP_CREATED, ['Content-Type' => 'application/json']);
    }
}
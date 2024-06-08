<?php
namespace App\Controller;

use App\Entity\User;
use App\Message\UserRegisteredMessage;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
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
    public function createUser(Request $request, SerializerInterface $serializer, MessageBusInterface $bus): Response
    {
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

        return new Response('User created successfully', Response::HTTP_CREATED);
    }
}
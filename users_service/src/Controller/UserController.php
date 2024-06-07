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

class UserController extends AbstractController
{
    #[Route('/user', name: 'create_user', methods: ['POST'])]
    public function createUser(Request $request, SerializerInterface $serializer, MessageBusInterface $bus): Response
    {
        $user = $serializer->deserialize($request->getContent(), User::class, 'json');
        
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($user);
        $entityManager->flush();

        // Publish an event to RabbitMQ
        $bus->dispatch(new UserRegisteredMessage($user->getId()));

        return new Response('User created successfully', Response::HTTP_CREATED);
    }
}
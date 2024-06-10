<?php
namespace App\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class LogController extends AbstractController
{
    #[Route('/logs', name: 'show_logs', methods: ['GET'])]
    public function showLogs(): Response
    {
        $filePath = __DIR__ . '/../../public/user_registered.txt';

        if (!file_exists($filePath))
            return new Response('No logs found', Response::HTTP_NOT_FOUND);
        else
            return new Response(str_replace(PHP_EOL, '<br>', file_get_contents($filePath)), Response::HTTP_OK);
    }
}
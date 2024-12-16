<?php

namespace App\Controller;

use App\Repository\MovieRepository;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class HomeController extends AbstractController
{
    #[Route(path: '/', name: 'homepage')]
    public function index(
        MovieRepository $movieRepository
    ): Response
    {
        $movies = $movieRepository->findAll();

        return $this->render('index.html.twig', [
            'movies' => $movies
        ]);
    }
}
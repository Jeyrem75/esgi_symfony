<?php

namespace App\Controller\Movie;

use App\Entity\Movie;
use App\Entity\Serie;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class MovieController extends AbstractController
{
    #[Route('/movie/{id}', name: 'show_movie')]
    public function movie(Movie $movie): Response
    {
        return $this->render('movie/detail.html.twig', [
            'movie' => $movie,
        ]);
    }

    #[Route('/serie/{id}', name: 'show_serie')]
    public function serie(Serie $serie): Response
    {
        return $this->render('movie/detail_serie.html.twig', [
            'serie' => $serie,
        ]);
    }
}

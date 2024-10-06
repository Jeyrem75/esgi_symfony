<?php

declare(strict_types=1);

namespace App\Controller\Movie;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class MovieController extends AbstractController
{
    #[Route('/movie', name: 'movie')]
    public function movie(): Response
    {
        return $this->render('movie/detail.html.twig');
    }

    #[Route('/serie', name: 'serie')]
    public function series(): Response
    {
        return $this->render('movie/detail_serie.html.twig');
    }
}
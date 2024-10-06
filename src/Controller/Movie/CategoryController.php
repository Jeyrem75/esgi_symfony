<?php

declare(strict_types=1);

namespace App\Controller\Movie;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class CategoryController extends AbstractController
{
    #[Route('/discover', name: 'discover')]
    public function index(): Response
    {
        return $this->render('movie/discover.html.twig');
    }

    #[Route('/category', name: 'category')]
    public function show(): Response
    {
        return $this->render('movie/category.html.twig');
    }
}
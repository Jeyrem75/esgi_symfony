<?php

namespace App\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route(path: '/admin')]
class AdminController extends AbstractController
{
    #[Route(path: '/', name: 'admin')]
    public function homepage(): Response
    {
        return $this->render('admin/homepage.html.twig');
    }

    #[Route(path: '/movies', name: 'admin_movies')]
    public function movies(): Response
    {
        return $this->render('admin/admin_films.html.twig');
    }

    #[Route(path: '/movies/add', name: 'admin_movies_add')]
    public function movies_add(): Response
    {
        return $this->render('admin/admin_add_films.html.twig');
    }

    #[Route(path: '/users', name: 'admin_users')]
    public function users(): Response
    {
        return $this->render('admin/admin_users.html.twig');
    }
}

<?php

namespace App\Controller\Other;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class PhotoController extends AbstractController
{
    #[Route(path: '/upload', name: "upload")]
    public function upload(): Response
    {
        return $this->render('other/upload.html.twig');
    }
}

<?php

declare(strict_types=1);

namespace App\Controller\Other;

use App\Entity\PlaylistSubscription;
use App\Repository\PlaylistRepository;
use App\Repository\PlaylistSubscriptionRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ListController extends AbstractController
{
    #[Route(path: '/lists', name: 'show_my_list')]
    public function show(
        PlaylistRepository $playlistRepository,
        PlaylistSubscriptionRepository $playlistSubscriptionRepository,
        Request $request,
    ): Response
    {
        $playlistId = $request->query->get('playlist');
        $playlist = $playlistRepository->find($playlistId);

        $playlits = $playlistRepository->findAll();
        $subscribedPlaylists = $playlistSubscriptionRepository->findAll();
        
        return $this->render('other/lists.html.twig', [
            'playlists' => $playlits,
            'subscribedPlaylists' => $subscribedPlaylists,
            'activePlaylist' => $playlist,
        ]);
    }
}
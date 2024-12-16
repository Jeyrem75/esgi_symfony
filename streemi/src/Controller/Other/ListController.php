<?php

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
        $user = $this->getUser();

        if (!$user) {
            return $this->redirectToRoute('app_login');
        }

        $playlistId = $request->query->get('playlist');

        if ($playlistId) {
            $playlist = $playlistRepository->find($playlistId);
        } else {
            $playlist = null;
        }

        $playlists = $playlistRepository->findAll();
        $subscribedPlaylists = $playlistSubscriptionRepository->findBy(['subscriber' => $user]);

        return $this->render('other/lists.html.twig', [
            'playlists' => $playlists,
            'subscribedPlaylists' => $subscribedPlaylists,
            'activePlaylist' => $playlist,
        ]);
    }
}

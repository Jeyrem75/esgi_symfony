<?php

namespace App\DataFixtures;

use App\Entity\Category;
use App\Entity\Comment;
use App\Entity\Episode;
use App\Entity\Language;
use App\Entity\Media;
use App\Entity\Movie;
use App\Entity\Playlist;
use App\Entity\PlaylistMedia;
use App\Entity\PlaylistSubscription;
use App\Entity\Season;
use App\Entity\Serie;
use App\Entity\Subscription;
use App\Entity\SubscriptionHistory;
use App\Entity\User;
use App\Entity\WatchHistory;
use App\Enum\CommentStatusEnum;
use App\Enum\UserAccountStatusEnum;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use DateTimeImmutable;

class AppFixtures extends Fixture
{
    public function __construct(
        private UserPasswordHasherInterface $passwordHasher
    ) {}

    public function load(ObjectManager $manager): void
    {
        $categories = $this->createCategories($manager);
        $languages = $this->createLanguages($manager);
        $subscriptions = $this->createSubscriptions($manager);
        $users = $this->createUsers($manager, $subscriptions);
        $medias = $this->createMedias($manager, $categories, $languages);
        $playlists = $this->createPlaylists($manager, $users, $medias);
        $this->createSubscriptionHistories($manager, $users, $subscriptions);
        $this->createComments($manager, $users, $medias);
        $this->createPlaylistSubscriptions($manager, $users, $playlists);
        $this->createWatchHistories($manager, $users, $medias);

        $manager->flush();
    }

    private function createCategories(ObjectManager $manager): array
    {
        $categoriesData = ['Action', 'Aventure', 'Drame', 'Comédie', 'Science-fiction'];
        $categories = [];

        foreach ($categoriesData as $categoryName) {
            $category = new Category();
            $category->setName($categoryName)->setLabel($categoryName);
            $manager->persist($category);
            $categories[] = $category;
        }

        return $categories;
    }

    private function createLanguages(ObjectManager $manager): array
    {
        $languagesData = [
            ['name' => 'Français', 'code' => 'fr'],
            ['name' => 'Anglais', 'code' => 'en'],
            ['name' => 'Espagnol', 'code' => 'es'],
            ['name' => 'Allemand', 'code' => 'de'],
            ['name' => 'Italien', 'code' => 'it']
        ];

        $languages = [];
        foreach ($languagesData as $lang) {
            $language = new Language();
            $language->setName($lang['name'])->setCode($lang['code']);
            $manager->persist($language);
            $languages[] = $language;
        }

        return $languages;
    }

    private function createUsers(ObjectManager $manager, array $subscriptions): array
    {
        $users = [];

        for ($i = 1; $i <= 10; $i++) {
            $user = new User();
            $user->setEmail("user$i@example.com")
                ->setUsername("user$i")
                ->setPassword($this->passwordHasher->hashPassword($user, 'password'))
                ->setAccountStatus(UserAccountStatusEnum::ACTIVE);
                if ($i === 1) {
                    $user->setRoles(['ROLE_ADMIN']);
                } elseif ($i <= 7) {
                    $user->setRoles(['ROLE_USER']);
                } else {
                    $user->setRoles(['ROLE_BANNED']);
                }

                if ($i <= 7) {
                    $user->setCurrentSubscription($subscriptions[array_rand($subscriptions)]);
                };

            $manager->persist($user);
            $users[] = $user;
        }

        return $users;
    }

    private function createSubscriptions(ObjectManager $manager): array
    {
        $subscriptionsData = [
            ['name' => 'Basic', 'duration' => 1, 'price' => 9.99],
            ['name' => 'Standard', 'duration' => 3, 'price' => 19.99],
            ['name' => 'Premium', 'duration' => 12, 'price' => 49.99]
        ];

        $subscriptions = [];
        foreach ($subscriptionsData as $data) {
            $subscription = new Subscription();
            $subscription->setName($data['name'])
                ->setDurationInMonths($data['duration'])
                ->setPrice($data['price']);

            $manager->persist($subscription);
            $subscriptions[] = $subscription;
        }

        return $subscriptions;
    }

    private function createMedias(ObjectManager $manager, array $categories, array $languages): array
    {
        $medias = [];

        for ($i = 1; $i <= 10; $i++) {
            $media = $i % 2 === 0 ? new Movie() : new Serie();
            $media->setTitle("Media $i")
                ->setShortDescription("Short description for media $i")
                ->setLongDescription("Long description for media $i")
                ->setReleaseDate(new DateTimeImmutable())
                ->setCoverImage("https://picsum.photos/200/300?random=$i");

            foreach (array_rand($categories, 2) as $key) {
                $media->addCategory($categories[$key]);
            }

            foreach (array_rand($languages, 2) as $key) {
                $media->addLanguage($languages[$key]);
            }

            if ($media instanceof Serie) {
                $this->createSeasons($manager, $media);
            }

            $manager->persist($media);
            $medias[] = $media;
        }

        return $medias;
    }

    private function createSeasons(ObjectManager $manager, Serie $serie): void
    {
        for ($i = 1; $i <= 3; $i++) {
            $season = new Season();
            $season->setSeasonNumber($i)->setSerie($serie);
            $manager->persist($season);

            $this->createEpisodes($manager, $season);
        }
    }

    private function createEpisodes(ObjectManager $manager, Season $season): void
    {
        for ($i = 1; $i <= 5; $i++) {
            $episode = new Episode();
            $episode->setTitle("Episode $i")
                ->setDuration((new \DateTime())->setTime(0, random_int(20, 60)))
                ->setReleaseDate(new DateTimeImmutable())
                ->setSeason($season);

            $manager->persist($episode);
        }
    }

    private function createPlaylists(ObjectManager $manager, array $users, array $medias): array
    {
        $playlists = [];

        foreach ($users as $user) {
            for ($i = 1; $i <= 2; $i++) {
                $playlist = new Playlist();
                $playlist->setName("Playlist $i of {$user->getUsername()}")
                    ->setCreatedBy($user)
                    ->setCreatedAt(new DateTimeImmutable())
                    ->setUpdatedAt(new DateTimeImmutable());

                foreach (array_rand($medias, 3) as $key) {
                    $playlistMedia = new PlaylistMedia();
                    $playlistMedia->setPlaylist($playlist)
                        ->setMedia($medias[$key])
                        ->setAddedAt(new DateTimeImmutable());

                    $manager->persist($playlistMedia);
                }

                $manager->persist($playlist);
                $playlists[] = $playlist;
            }
        }

        return $playlists;
    }

    private function createPlaylistSubscriptions(ObjectManager $manager, array $users, array $playlists): void
    {
        foreach ($users as $user) {
            for ($i = 1; $i <= random_int(1, 3); $i++) {
                $playlistSubscription = new PlaylistSubscription();
                $playlistSubscription->setSubscriber($user)
                    ->setPlaylist($playlists[array_rand($playlists)])
                    ->setSubscribedAt(new DateTimeImmutable());

                $manager->persist($playlistSubscription);
            }
        }
    }

    private function createWatchHistories(ObjectManager $manager, array $users, array $medias): void
    {
        foreach ($users as $user) {
            for ($i = 1; $i <= random_int(1, 5); $i++) {
                $watchHistory = new WatchHistory();
                $watchHistory->setMedia($medias[array_rand($medias)])
                    ->setWatcher($user)
                    ->setLastWatched(new DateTimeImmutable())
                    ->setNumberOfViews(random_int(1, 10));

                $manager->persist($watchHistory);
            }
        }
    }

    private function createSubscriptionHistories(ObjectManager $manager, array $users, array $subscriptions): void
    {
        foreach ($users as $user) {
            $subscription = $subscriptions[array_rand($subscriptions)];

            $history = new SubscriptionHistory();
            $history->setSubscriber($user)
                ->setSubscription($subscription)
                ->setStartDate(new DateTimeImmutable('-1 month'))
                ->setEndDate(new DateTimeImmutable('+2 months'));

            $manager->persist($history);
        }
    }

    private function createComments(ObjectManager $manager, array $users, array $medias): void
    {
        foreach ($medias as $media) {
            for ($i = 1; $i <= 3; $i++) {
                $comment = new Comment();
                $comment->setContent("Comment $i for {$media->getTitle()}")
                    ->setWrittenBy($users[array_rand($users)])
                    ->setStatus(CommentStatusEnum::VALIDATED)
                    ->setMedia($media);

                $manager->persist($comment);
            }
        }
    }
}

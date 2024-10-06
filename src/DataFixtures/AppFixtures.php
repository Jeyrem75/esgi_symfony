<?php

namespace App\DataFixtures;

use App\Entity\Category;
use App\Entity\Language;
use App\Entity\Movie;
use App\Entity\Serie;
use App\Entity\User;
use App\Entity\WatchHistory;
use App\Enum\UserAccountStatusEnum;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $users = $this->generateUsers($manager);
        $medias = $this->generateMedias($manager);
        $categories = $this->generateCategories($manager, $medias);
        $languages = $this->generateLanguages($manager, $medias);

        $manager->flush();
    }

    private function generateUsers(ObjectManager $manager)
    {
        $users = [];
        for ($i = 0; $i < random_int(10, 20); $i++) {
            $user = new User();
            $user->setUsername("user_{$i}");
            $user->setEmail("email_{$i}@example.com");
            $user->setPassword('motdepasse');
            $user->setAccountStatus(UserAccountStatusEnum::ACTIVE);
            $users[] = $user;
            $manager->persist($user);
        }

        return $users;
    }

    protected function generateLanguages(ObjectManager $manager, array $medias): array
    {
        $tabs = [['fr', 'Français'], ['en', 'Anglais'], ['es', 'Espagnol'], ['de', 'Allemand'], ['it', 'Italien'], ['pt', 'Portugais'], ['ru', 'Russe'], ['zh', 'Chinois'], ['ja', 'Japonais'], ['ar', 'Arabe'], ['hi', 'Hindi'], ['bn', 'Bengali'], ['pt', 'Portugais'], ['ru', 'Russe'], ['ko', 'Coréen'], ['tr', 'Turc'], ['nl', 'Néerlandais'], ['pl', 'Polonais'], ['vi', 'Vietnamien'], ['sv', 'Suédois'], ['cs', 'Tchèque'], ['fi', 'Finnois'], ['el', 'Grec'], ['da', 'Danois'], ['no', 'Norvégien'], ['he', 'Hébreu'], ['id', 'Indonésien'], ['ms', 'Malais'], ['th', 'Thaï'], ['hu', 'Hongrois'], ['sk', 'Slovaque'], ['ro', 'Roumain'], ['bg', 'Bulgare'], ['uk', 'Ukrainien'], ['hr', 'Croate'], ['ca', 'Catalan'], ['sr', 'Serbe'], ['lt', 'Lituanien'], ['sl', 'Slovène'], ['et', 'Estonien'], ['lv', 'Letton'], ['mk', 'Macédonien'], ['sq', 'Albanais'], ['hy', 'Arménien'], ['ka', 'Géorgien'], ['uz', 'Ouzbek'], ['kk', 'Kazakh'], ['az', 'Azéri'], ['tg', 'Tadjik'], ['tk', 'Turkmène'], ['mn', 'Mongol'], ['ky', 'Kirghiz'], ['si', 'Sinhala'], ['am', 'Amharique'], ['km', 'Khmer'], ['lo', 'Lao'], ['my', 'Birman'],];
        $languages = [];

        foreach ($tabs as $tab) {
            $entity = new Language();
            $entity->setCode($tab[0]);
            $entity->setNom($tab[1]);
            $manager->persist($entity);
            $languages[] = $entity;

            for ($k = 0; $k < random_int(0, 20); $k++) {
                $media = $medias[array_rand($medias)];
                $media->addLanguage($entity);
            }
        }

        return $languages;
    }

    protected function generateCategories(ObjectManager $manager, array $medias): array
    {
        $categories = [];
        $tabs = ['Action', 'Aventure', 'Comédie', 'Drame', 'Fantastique', 'Horreur', 'Policier', 'Science-fiction', 'Thriller'];
        foreach ($tabs as $tab) {
            $category = new Category();
            $category->setLabel($tab);
            $category->setNom(strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $tab)));
            $manager->persist($category);
            $categories[] = $category;

            for ($k = 0; $k < random_int(0, 20); $k++) {
                $media = $medias[array_rand($medias)];
                $media->addCategory($category);
            }
        }

        return $categories;
    }

    protected function generateMedias(ObjectManager $manager): array
    {
        /** @var array<Movie|Serie> $medias */
        $medias = [];
        for ($j = 0; $j < random_int(10, 20); $j++) {
            $movie = new Movie();
            $movie->setTitle("movie_{$j}");
            $movie->setShortDescription("short description for movie_{$j}");
            $movie->setLongDescription("long description for movie_{$j}");
            $movie->setCoverImage("cover_image_{$j}.png");
            $movie->setReleaseDate(new \DateTime());
            $movie->setCasting([]);
            $movie->setStaff([]);
            $medias[] = $movie;
            $manager->persist($movie);
        }

        for ($j = 0; $j < random_int(10, 20); $j++) {
            $serie = new Serie();
            $serie->setTitle("serie_{$j}");
            $serie->setShortDescription("short description for serie_{$j}");
            $serie->setLongDescription("long description for serie_{$j}");
            $serie->setCoverImage("cover_image_{$j}.png");
            $serie->setReleaseDate(new \DateTime());
            $serie->setCasting([]);
            $serie->setStaff([]);
            $medias[] = $serie;
            $manager->persist($serie);
        }
        return $medias;
    }
}

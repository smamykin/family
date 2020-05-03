<?php

namespace App\DataFixtures;

use App\Entity\Category;
use App\Entity\Video;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectManager;

class VideoFixtures extends Fixture
{
    private const BASE_PATH = 'https://player.vimeo.com/video/';

    /**
     * @param ObjectManager $manager
     * @throws \Exception
     */
    public function load(ObjectManager $manager)
    {
        foreach ($this->videoData($manager) as [$title, $path, $categoryId]) {
            $duration = random_int(10, 300);
            $category = $manager->getRepository(Category::class)->find($categoryId);

            $video = new Video();
            $video->setTitle($title)
                ->setPath(self::BASE_PATH . $path)
                ->setCategory($category)
                ->setDuration($duration);
            $manager->persist($video);
        }

        $manager->flush();
    }

    private function videoData(ObjectManager $manager)
    {
        $rep = $manager->getRepository(Category::class);
        $moviesId = $rep->findOneBy(['name' => 'Movies'])->getId();
        $familyId = $rep->findOneBy(['name' => 'Family'])->getId();
        $romanticId = $rep->findOneBy(['name' => 'Romantic Comedy'])->getId();
        $dramaId = $rep->findOneBy(['name' => 'Romantic Drama'])->getId();
        $toyId =  $rep->findOneBy(['name' => 'Toys'])->getId();

        return [
            ['Movies 1', 289729765, $moviesId],
            ['Movies 2', 238902809, $moviesId],
            ['Movies 3', 150870038, $moviesId],
            ['Movies 4', 219727723, $moviesId],
            ['Movies 5', 289879647, $moviesId],
            ['Movies 6', 261379936, $moviesId],
            ['Movies 7', 289029793, $moviesId],
            ['Movies 8', 60594348, $moviesId],
            ['Movies 9', 290253648, $moviesId],
            ['Family 1', 289729765, $familyId],
            ['Family 2', 289729765, $familyId],
            ['Family 3', 289729765, $familyId],
            ['Romantic comedy 1', 289729765, $romanticId],
            ['Romantic comedy 2', 289729765, $romanticId],
            ['Romantic drama 1', 289729765, $dramaId],
            ['Toys  1', 289729765, $toyId],
            ['Toys  2', 289729765, $toyId],
            ['Toys  3', 289729765, $toyId],
            ['Toys  4', 289729765, $toyId],
            ['Toys  5', 289729765, $toyId],
            ['Toys  6', 289729765, $toyId]
        ];
    }
}

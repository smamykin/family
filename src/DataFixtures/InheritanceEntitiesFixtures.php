<?php

namespace App\DataFixtures;

use App\Entity\Author;
use App\Entity\File;
use App\Entity\Pdf;
use App\Entity\VideoFile;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Faker\Factory;
use Faker\ORM\Doctrine\Populator;

class InheritanceEntitiesFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        /** @var Author[] $authors */
        $authors = [new Author(), new Author()];
        $faker = Factory::create();
        foreach ($authors as $author) {
            $author->setName($faker->name);
            $populator = new Populator($faker,$manager);
            $populator->addEntity(VideoFile::class, 2);
            $populator->addEntity(Pdf::class, 3);

            $files = $populator->execute();

            $carry = [];
            foreach ($files as $file) {
                if (! is_array($file)) {
                    $carry[] = $file;
                } else {
                    array_push($carry, ...array_values($file));
                }
            }

            /** @var File $file */
            foreach ($carry as $file) {
                $author->addFile($file);
            }
            $manager->persist($author);
        }
        $manager->flush();
    }
}

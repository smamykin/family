<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Faker\Factory;
use Faker\ORM\Doctrine\Populator;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $generator = Factory::create();
        $pop = new Populator($generator,$manager);
        $pop->addEntity(User::class, 4);
        $ents = $pop->execute();
        $ents = reset($ents);
        $user = array_shift($ents);
        $this->addReference('user', $user);
    }
}

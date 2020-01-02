<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        for ($i = 4; $i--;) {
            $user = new User();
            $user->setName('some new name');
            $manager->persist($user);
        }

        $manager->flush();
    }
}

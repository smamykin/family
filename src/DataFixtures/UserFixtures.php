<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserFixtures extends Fixture
{
    /**
     * @var UserPasswordEncoderInterface
     */
    private $passwordEncoder;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }

    public function load(ObjectManager $manager)
    {
        foreach ($this->getUserData() as [$name, $lastName, $email, $password, $apiKey, $roles]) {
            $user = new User();
            $user->setName($name);
            $user->setLastName($lastName);
            $user->setEmail($email);
            $user->setPassword($this->getPasswordEncoder()->encodePassword($user, $password));
            $user->setVimeoApiKey($apiKey);
            $user->setRoles($roles);
            $manager->persist($user);
        }
        $manager->flush();
    }

    private function getUserData(): array
    {
        return [
            ['John', 'Wayne', 'jw@symf4.loc', 'passw', 'hjd8dehdh', ['ROLE_ADMIN']],
            ['John', 'Wayne2', 'jw2@symf4.loc', 'passw', null, ['ROLE_ADMIN']],
            ['John', 'Doe', 'jd@symf4.loc', 'passw', null, ['ROLE_USER']]
        ];
    }

    /**
     * @return UserPasswordEncoderInterface
     */
    private function getPasswordEncoder(): UserPasswordEncoderInterface
    {
        return $this->passwordEncoder;
    }
}

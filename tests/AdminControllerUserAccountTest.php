<?php

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use App\Entity\User;

class AdminControllerUserAccountTest extends WebTestCase
{
    use RollUser;

    public function testUserDeletedAccount()
    {
        $this->client->request('GET', '/admin/delete-account');

        $user = $this->em->getRepository(User::class)->find(3);
        $this->assertNull($user);
    }

    public function testUserChangedPassword()
    {

        $crawler = $this->client->request('GET', '/admin/');

        $form = $crawler->selectButton('Save')->form([
            'user[name]' => 'name',
            'user[last_name]' => 'last name',
            'user[email]' => 'email@email.email',
            'user[password][first]' => 'password',
            'user[password][second]' => 'password'
        ]);
        $this->client->submit($form);

        $user = $this->em->getRepository(User::class)->find(3);

        $this->assertSame('name',$user->getName());
    }
}

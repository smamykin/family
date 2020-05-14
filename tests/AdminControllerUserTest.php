<?php

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use App\Entity\User;

class AdminControllerUserTest extends WebTestCase
{
    use RollAdmin;

    public function testUserDeleted()
    {
        $this->client->request('GET', '/admin/su/delete-user/4');
        $user = $this->em->getRepository(User::class)->find(4);
        $this->assertNull($user);
    }
}


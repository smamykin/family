<?php

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

use App\Entity\Video;

class AdminControllerVideosTest extends WebTestCase
{
    use RollAdmin;

    public function testDeleteVideo()
    {
        $this->client->request('GET', '/admin/su/delete-video/11/289729765');
        $video = $this->em->getRepository(Video::class)->find(11);
        $this->assertNull($video);
    }

}

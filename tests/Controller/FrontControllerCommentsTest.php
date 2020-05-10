<?php

namespace App\Tests;

use App\Entity\Video;
use App\Repository\VideoRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class FrontControllerCommentsTest extends WebTestCase
{
    use RollAdmin;

    public function testCannotCommentWithoutAuth()
    {
        $client = static::createClient();
        $client->followRedirects();

        $video = $this->em->getRepository(Video::class)->findOneBy([]);
        $crawler = $client->request('GET','video-details/' . $video->getId());
        $form = $crawler->selectButton('Add')->form([
            'comment' => 'Test Comment',
        ]);
        $client->submit($form);

        $this->assertSelectorTextContains('body', 'Please sign in');
    }

    public function testCreateComment()
    {
        $client = $this->client;
        $client->followRedirects();

        /** @var Video $video */
        $video = $this->em->getRepository(Video::class)->findOneBy([]);
        $count = $video->getComments()->count();
        $crawler = $client->request('GET','video-details/' . $video->getId());
        $form = $crawler->selectButton('Add')->form([
            'comment' => 'Test Comment',
        ]);
        $client->submit($form);

        $this->assertSelectorTextContains('body', 'Test Comment');

        $crawler = $client->request('GET','/video-list/'. $video->getCategory()->getName() .','. $video->getCategory()->getId());

        $this->assertSelectorTextContains('body', 'Comments (' . ($count + 1) . ')');
    }
}


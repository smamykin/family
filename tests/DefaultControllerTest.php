<?php

namespace App\Tests;

use App\Entity\Video;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class DefaultControllerTest extends WebTestCase
{
    private $em;
    /**
     * @var KernelBrowser
     */
    private $client;

    protected function setUp()
    {
        parent::setUp();
        $this->client = static::createClient();

        $this->em = $this->client->getContainer()->get('doctrine.orm.entity_manager');
        $this->em->beginTransaction();
        $this->em->getConnection()->setAutoCommit(false);
    }
    protected function tearDown()
    {
        $this->em->rollback();
        $this->em->close();
        $this->em = null;
    }

    public function testSomething()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/status-show');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'ok');

        $link = $crawler
            ->filter('a:contains("awesome link")')
            ->link();

        $crawler = $client->click($link);
        $this->assertContains('Remember me', $client->getResponse()->getContent());
    }

    public function testLogin()
    {

        $client = static::createClient();
        $crawler = $client->request('GET', '/login');

        $form = $crawler->selectButton('login')->form();
        $form['email'] = 'admin3@email.ru';
        $form['password'] = '1234';

        $crawler = $client->submit($form);
        $crawler = $client->followRedirect();

        $this->assertSame(1, $crawler->filter('a:contains("logout")')->count());
    }

    /**
     * @dataProvider provideUrls
     */
    public function testSomething2($url)
    {
        $crawler = $this->client->request('GET', $url);
        $this->assertResponseIsSuccessful();
        $video = $this->em
            ->getRepository(Video::class)
            ->find(1);

        $this->em->remove($video);
        $this->em->flush();


    }

    public function provideUrls()
    {
        return [
            ['/login'],
            ['/status-show'],
        ];
    }
}

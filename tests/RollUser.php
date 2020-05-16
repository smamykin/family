<?php

namespace App\Tests;

use App\Utils\Interfaces\CacheInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;

trait RollUser
{
    /**
     * @var ObjectManager
     */
    private $em;

    /**
     * @var KernelBrowser
     */
    private $client;
    private $cache;

    protected function setUp(): void
    {
        parent::setUp();
        self::bootKernel();
        $container = self::$container;
        $cache = $container->get(CacheInterface::class);
        $this->cache = $cache->cache;
        $this->cache->clear();

        $this->client = static::createClient([],[
            'PHP_AUTH_USER' => 'jd@symf4.loc',
            'PHP_AUTH_PW' => 'passw',
        ]);
        $this->client->disableReboot();
        $this->em = $this->client->getContainer()->get('doctrine.orm.entity_manager');
        $this->em->beginTransaction();
        $this->em->getConnection()->setAutoCommit(false);
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        $this->cache->clear();
        $this->em->close();
        $this->em = null;
    }
}

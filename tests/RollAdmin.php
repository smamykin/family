<?php

namespace App\Tests;

use Doctrine\Persistence\ObjectManager;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;

trait RollAdmin
{
    /**
     * @var ObjectManager
     */
    private $em;

    /**
     * @var KernelBrowser
     */
    private $client;
    protected function setUp(): void
    {
        parent::setUp();
        $this->client = static::createClient([],[
            'PHP_AUTH_USER' => 'jw@symf4.loc',
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
        $this->em->close();
        $this->em = null;
    }
}

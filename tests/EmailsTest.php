<?php

namespace App\Tests;

use Swift_Message;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class EmailsTest extends WebTestCase
{
    public function testSomething()
    {
        $client = static::createClient();
        $client->enableProfiler();

        $crawler = $client->request('GET', '/mailing');

        $mailCollector = $client->getProfile()->getCollector('swiftmailer');

        $this->assertSame(1, $mailCollector->getMessageCount());

        $collectedMessages = $mailCollector->getMessages();
        /** @var Swift_Message $message */
        $message = $collectedMessages[0];

        $this->assertSame('Hello Email', $message->getSubject());
        $this->assertSame('send@example.com', key($message->getFrom()));
        $this->assertSame('recipient@example.com', key($message->getTo()));
        $this->assertContains('You did it! You registered!', $message->getBody());
    }
}

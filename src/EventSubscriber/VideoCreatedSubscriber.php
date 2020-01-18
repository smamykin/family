<?php

namespace App\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class VideoCreatedSubscriber implements EventSubscriberInterface
{
    public function onVideoCreatedEvent($event)
    {
        dump($event->video->title);
    }

    public static function getSubscribedEvents()
    {
        return [
//            'video.created.event' => 'onVideoCreatedEvent',
//            KernelEvents::RESPONSE => [
//                ['onKernelEvent1', 10],
//                ['onKernelEvent2', 2],
//            ],
        ];
    }

    public function onKernelEvent1(ResponseEvent $event)
    {
        dump('1');
    }

    public function onKernelEvent2(ResponseEvent $event)
    {
        dump('2');
    }
}

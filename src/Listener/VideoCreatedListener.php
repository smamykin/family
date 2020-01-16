<?php

namespace App\Listener;

use App\Event\VideoCreatedEvent;

class VideoCreatedListener
{
    public function onVideoCreatedEvent(VideoCreatedEvent $event)
    {
        dump('hi');
    }
}

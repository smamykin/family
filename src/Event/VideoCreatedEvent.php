<?php

namespace App\Event;


use Symfony\Contracts\EventDispatcher\Event;

class VideoCreatedEvent extends Event
{
    public $video;

    public function __construct($video)
    {
        $this->video = $video;
    }
}

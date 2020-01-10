<?php

namespace App\Services;

class MyService
{
    public $my;
    public $notMy;
    public $logger;


    public function someAction()
    {
        dump($this->my, $this->notMy, $this->logger);
    }
}

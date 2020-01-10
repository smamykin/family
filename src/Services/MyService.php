<?php

namespace App\Services;

class MyService
{
    /**
     * @var MyService2
     */
    public $service;

    public function __construct(MyService2 $service)
    {
        dump($service);
        $this->service = $service;
    }

    public function someAction()
    {
        $this->service->someAction();
        dump($this->service);
    }
}

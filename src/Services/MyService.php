<?php

namespace App\Services;

class MyService
{
    use MyServiceTrait;

    public function someAction()
    {
        $this->service->someAction();
    }
}

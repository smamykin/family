<?php

namespace App\Services;

class MyService2
{
    public function __construct()
    {
        dump('second service constructor');
    }

    public function someAction()
    {
        dump('wow');
    }
}

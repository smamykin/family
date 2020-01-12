<?php

namespace App\Services;

class MyService2 implements ServiceInterface
{
    public function __construct()
    {
        dump('second service constructor');
    }
}

<?php

namespace App\Services;

class MyService implements ServiceInterface
{
    public function __construct()
    {
        dump('hello, it\'s constructor');
    }

    public function postFlush()
    {
        dump('postFlush ...');
    }

    public function clear()
    {
        dump('clear...');
    }
}

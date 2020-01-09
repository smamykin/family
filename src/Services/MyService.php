<?php

namespace App\Services;

class MyService
{
    public function __construct($param1, $globalParameter)
    {
        dump($param1, $globalParameter);
    }
}

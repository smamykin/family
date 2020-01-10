<?php


namespace App\Services;


trait MyServiceTrait
{
    /**
     * @var MyService2
     */
    protected $service;

    /**
     * @required
     * @param MyService2 $service
     */
    public function setMyService2(MyService2 $service)
    {
        $this->service = $service;
    }
}

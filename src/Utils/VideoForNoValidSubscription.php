<?php

namespace App\Utils;

use App\Entity\User;
use Datetime;
use Symfony\Component\Security\Core\Security;
use App\Entity\Video;

class VideoForNoValidSubscription  {

    public $isSubscriptionValid = false;

    public function __construct(Security $security)
    {
        /** @var User $user */
        $user = $security->getUser();
        if($user && $user->getSubscription() != null)
        {
            $payment_status = $user->getSubscription()->getPaymentStatus();
            $valid = new Datetime()  <  $user->getSubscription()->getValidTo() ;

            if($payment_status != null && $valid)
            {
                $this->isSubscriptionValid = true;
            }
        }

    }

    public function check()
    {
        return $this->isSubscriptionValid ? null : Video::videoForNotLoggedInOrNoMembers;
    }
}

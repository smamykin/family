<?php

namespace App\DataFixtures;

use App\Entity\Subscription;
use App\Entity\User;
use Datetime;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class SubscriptionFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        foreach ($this->getSubscriptionData() as [$user_id, $plan, $valid_to, $payment_status, $free_plan_used]) {
            $subscription = new Subscription();
            $subscription->setPlan($plan)
                ->setValidTo($valid_to)
                ->setPaymentStatus($payment_status)
                ->setFreePlanUsed($free_plan_used);

            /** @var User $user */
            $user = $this->getReference($user_id);
            $user->setSubscription($subscription);

            $manager->persist($user);
        }
        $manager->flush();
    }

    private function getSubscriptionData(): array
    {
        return [
            [
                UserFixtures::USER_REFERENCE . 1,
                Subscription::getPlanDataNameByIndex(2),
                (new Datetime())->modify('+100 year'),
                'paid',
                false
            ], // super admin
            [
                UserFixtures::USER_REFERENCE . 3,
                Subscription::getPlanDataNameByIndex(0),
                (new Datetime())->modify('+1 month'),
                'paid',
                true
            ],
            [
                UserFixtures::USER_REFERENCE . 4,
                Subscription::getPlanDataNameByIndex(1),
                (new Datetime())->modify('+1 minute'),
                'paid',
                false
            ]
        ];
    }
}

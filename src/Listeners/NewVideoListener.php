<?php

namespace App\Listeners;

use Doctrine\Common\Persistence\Event\LifecycleEventArgs;
use App\Entity\Video;
use App\Entity\User;
use Swift_Mailer;
use Swift_Message;
use Twig\Environment;


class NewVideoListener
{

    /**
     * @var Environment
     */
    private $templating;
    /**
     * @var Swift_Mailer
     */
    private $mailer;

    public function __construct(Environment $templating, Swift_Mailer $mailer)
    {
        $this->templating = $templating;
        $this->mailer = $mailer;
    }

    /**
     * @param LifecycleEventArgs $args
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    public function postPersist(LifecycleEventArgs $args)
    {

        $entity = $args->getObject();

        if (!$entity instanceof Video) {
            return;
        }


        $entityManager = $args->getObjectManager();

        $users = $entityManager->getRepository(User::class)->findAll();

        foreach($users as $user)
        {
            $message = (new Swift_Message('Hello Email'))
                ->setFrom('send@example.com')
                ->setTo($user->getEmail())
                ->setBody(
                    $this->templating->render(
                        'emails/new_video.html.twig',
                        [
                            'name' => $user->getName(),
                            'video' => $entity
                        ]
                    ),
                    'text/html'
                );

            $this->mailer->send($message);
        }

    }
}

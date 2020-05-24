<?php

namespace App\Listener;

use App\Event\ResetPasswordRequestEvent;
use Swift_Mailer;
use Swift_Message;

class ResetPasswordRequestListener
{
    /**
     * @var Swift_Mailer
     */
    private $mailer;

    public function __construct(Swift_Mailer $mailer)
    {
        $this->mailer = $mailer;
    }

    public function onResetPasswordRequestedAction(ResetPasswordRequestEvent $event)
    {
        $userId = $event->getUser()->getId();
        $message = (new Swift_Message('New password request'))
            ->setFrom('admin@example.com')
            ->setTo('user@example.com')
            ->setBody(
                'You requested a new password. User this link to reset your password:
                http://family.loc/api/users/' . $userId . '/change-password?token=' . $event->getUser()->getLostPassword()->getToken() . '&password=here_new_password'
            );
        $this->mailer->send($message);
    }

}

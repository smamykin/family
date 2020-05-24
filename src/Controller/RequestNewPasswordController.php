<?php

namespace App\Controller;

use App\Entity\LostPassword;
use App\Entity\User;
use App\Event\ResetPasswordRequestEvent;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

//use Symfony\Component\Routing\Annotation\Route;

class RequestNewPasswordController extends AbstractController
{
    /**
     * @var EventDispatcherInterface
     */
    private $dispatcher;

    public function __construct(EventDispatcherInterface $dispatcher)
    {
        $this->dispatcher = $dispatcher;
    }

    public function __invoke(LostPassword $data)
    {
        $em = $this->getDoctrine()->getManager();
        $rep = $em->getRepository(User::class );
        $user = $rep->findOneBy(['email'=>$data->getEmail()]);

        if ($user) {
            /** @var $user User */
            $user->setLostPassword($data);
            $em->flush();

            $dispatcher = $this->dispatcher;
            $event = new ResetPasswordRequestEvent($user);

            $dispatcher->dispatch($event, ResetPasswordRequestEvent::NAME);

        } else {
            exit;
        }


        return new JsonResponse($user->getId());
    }
}

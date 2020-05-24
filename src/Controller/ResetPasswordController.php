<?php

namespace App\Controller;

use App\Entity\LostPassword;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class ResetPasswordController extends AbstractController
{
    public function __invoke(Request $request, User $user, UserPasswordEncoderInterface $encoder)
    {
        if ($request->get('token') === $user->getLostPassword()->getToken()) {
            $em = $this->getDoctrine()->getManager();
            $user->setPassword($encoder->encodePassword($user, $request->get('password')));
            $rep = $this->getDoctrine()->getRepository(LostPassword::class);
            $item = $rep->find($user->getLostPassword()->getId());
            $em->remove($item);
            $em->persist($user);
            $em->flush();

            return new Response(sprintf('User password %s successfully updated', $user->getUsername()));
        }
        return new Response('Invalid data');
    }
}

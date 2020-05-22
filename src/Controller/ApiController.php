<?php

namespace App\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class ApiController extends AbstractController
{
    public function register(Request $request, UserPasswordEncoderInterface $encoder)
    {
        $em = $this->getDoctrine()->getManager();
        $email = $request->request->get('email');
        $password = $request->request->get('password');
        $user = new User();
        $user->setPassword($encoder->encodePassword($user, $password))
            ->setEmail($email);

        $em->persist($user);
        $em->flush();

        return new Response(sprintf('User %s successfully created', $user->getUsername()));
    }

    public function api()
    {
        return new Response(sprintf('Logged in as %s', $this->getUser()->getUsername()));
    }
}

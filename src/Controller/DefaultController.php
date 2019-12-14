<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class DefaultController extends AbstractController
{
    /**
     * @Route("/", name="index")
     */
    public function index()
    {
        $users = ['adam', 'Robert' ];
        return $this->render('default/index.html.twig', [
            'controller_name' => 'DefaultController',
            'users' => $users,
        ]);
    }

    /**
     * @Route("/generate_url/{param?}/",name="generate-url")
     */
    public function generateUrlAction($param)
    {
        exit($this->generateUrl(
            'generate-url',
            ['param' => 11],
            UrlGeneratorInterface::ABSOLUTE_PATH
        ));
    }
}

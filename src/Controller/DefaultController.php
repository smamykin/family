<?php

namespace App\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class DefaultController extends AbstractController
{
    /**
     * @Route("/", name="index")
     */
    public function index()
    {
        $users = $this->getDoctrine()->getRepository(User::class)->findAll();


        return $this->render('default/index.html.twig', [
            'controller_name' => 'DefaultController',
            'users' => $users,
        ]);
    }

    /**
     * @Route("/generate_url/{param?}",name="generate-url")
     */
    public function generateUrlAction($param)
    {
        exit($this->generateUrl(
            'generate-url',
            ['param' => 11],
            UrlGeneratorInterface::ABSOLUTE_PATH
        ));
    }

    /**
     * @Route("/download", name="download-file")
     */
    public function downloadAction()
    {
        $path = $this->getParameter('download_directory');
        return $this->file($path . 'mathcs.pdf');
    }

    /**
     * @Route("/redirect-test")
     */
    public function redirectTestAction()
    {
        return $this->redirectToRoute('redirect-to-route', ['param' => 10]);
    }

    /**
     * @Route("/url-to-redirect/{param?}", name="redirect-to-route")
     * @param $param
     */
    public function methodToRedirectAction($param)
    {
        exit('test redirection' . $param);
    }

    /**
     * @Route("forward-to-controller")
     */
    public function forwardToControllerAction()
    {
        return $this->forward(
            self::class . '::methodToRedirectAction',
            ['param' => 1]
        );
    }

    public function mostPopularPosts($param)
    {
        return new Response('some new data' . $param);
    }
}

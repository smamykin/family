<?php

namespace App\Controller;

use App\Entity\User;
use App\Services\GiftsService;
use Doctrine\DBAL\Connection;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class DefaultController extends AbstractController
{
    /**
     * @var array
     */
    private $gifts;

    public function __construct(GiftsService $gifts, $logger)
    {
        $this->gifts = $gifts->gifts;
        $this->logger = $logger;
    }

    /**
     * @Route("/", name="index")
     * @return Response
     */
    public function index()
    {
        $rep = $this->getDoctrine()->getRepository(User::class);
        /**
         * @var $connection Connection
         */
        $connection = $this->getDoctrine()->getConnection();
//        $entityManager->flush();

        $sql = '
        SELECT * FROM user u
        WHERE u.id > :id';
        $stmt = $connection->prepare($sql);
        $stmt->execute(['id' => 1]);
        dump($stmt->fetchAll());

        $users = [];
        return $this->render(
            'default/index.html.twig',
            [
                'controller_name' => 'DefaultController',
                'users' => $users,
                'random_gift' => $this->gifts
            ]
        );
    }

    /**
     * @Route("/home/{name}", name="home")
     * @param Request $request
     * @param User $user
     * @return Response
     */
    public function homeAction(Request $request, User $user)
    {
        dump($user);
        return $this->render('default/home.html.twig', []);
    }

    /**
     * @Route(
     *     "/articles/{_locale}/{year}/{slug}/{category}",
     *     defaults={"category":"computers"},
     *     requirements={"_locale": "en|fr", "category":"computers|rtv","year":"\d+"}
     * )
     */
    public function index2()
    {
        return new Response('An advanced route example');
    }

    /**
     * @Route({"nl": "/over-ons","en":"about-us"}, name="about_us")
     */
    public function index3()
    {
        return new Response('Translated routes');
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

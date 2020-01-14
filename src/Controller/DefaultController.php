<?php

namespace App\Controller;

use App\Entity\Address;
use App\Entity\Author;
use App\Entity\File;
use App\Entity\Pdf;
use App\Entity\User;
use App\Entity\Video;
use App\Entity\VideoFile;
use App\Services\GiftsService;
use App\Services\MyService;
use App\Services\ServiceInterface;
use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Cache\CacheException;
use Psr\Cache\CacheItemInterface;
use Psr\Cache\InvalidArgumentException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Component\Cache\Adapter\TagAwareAdapter;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Contracts\Cache\ItemInterface;

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
     * @Route("/lifecicles", name="lifecicles")
     */
    public function lifeCiclesAction()
    {

//        $user = new User();
//        $user->setName('lifecicles-test - ' . mt_rand());
//        $em = $this->getDoctrine()->getManager();
//        for ($i = 5; $i--;) {
//            $video = new Video();
//            $video->setTitle('some video title - ' . (new \DateTimeImmutable())->format('Y.m.d H.i.s'));
//            $user->addVideo($video);
//        }
//
//        $em->persist($user);
//        $em->flush();
//        $video = $this->getDoctrine()->getRepository(Video::class)->find(3);
//        /** @var $video Video */
//        $user = $video->getUser();
//        $user->getName();

        /** @var User $user */
        $user = $this->getDoctrine()->getRepository(User::class)->find(1);
        foreach($user->getVideos() as $video) {
            $user->removeVideo($video);
            break;
        }
//        $this->getDoctrine()->getManager()->remove($user);
        $this->getDoctrine()->getManager()->flush();

        return $this->render('default/lifecicles-test.html.twig', ['user' => $user]);
    }

    /**
     * @Route("/relation", name="relation")
     * @return Response
     */
    public function relationAction()
    {
        $user  = new User();
        $user->setName('Adam');
        $address = new Address();
        $address->setNumber('4');
        $address->setStreet(1242);
        $user->setAddress($address);
        $em = $this->getDoctrine()->getManager();
//        $em->persist($address);
        $em->persist($user);
        $em->flush();

        $status = 'OK';
        return $this->render('default/relation.html.twig', ['status' => $status]);
    }

    /**
     * @Route("/many_to_many", name="many_to_many")
     */
    public function relationManyToManyAction()
    {
        $status = 'OK';
        $userRep = $this->getDoctrine()->getRepository(User::class);
        $users = $userRep->findAll();
        /** @var User $firstUser */
        $firstUser = array_shift($users);
//        foreach ($users as $user) {
//            /** @var User $user */
//            $firstUser->addFollowed($user);
//        }
//        $this->getDoctrine()->getManager()->flush();
        dump($firstUser->getFollowed()->count());
        dump($firstUser->getFollowing()->count());
        dump(array_shift($users)->getFollowing()->count());

        return $this->render('default/relation.html.twig', ['status' => $status]);
    }

    /**
     * @Route("/eager", name="eager")
     * @param ServiceInterface $service
     * @return Response
     * @throws InvalidArgumentException
     * @throws CacheException
     */
    public function eagerLoadingAction(ServiceInterface $service)
    {
        $status = 'OK';
        $cache = new TagAwareAdapter(
            new FilesystemAdapter()
        );

        $acer  = $cache->getItem('acer');
        $dell  = $cache->getItem('dell');
        $ibm  = $cache->getItem('ibm');
        $apple  = $cache->getItem('apple');
        if (!$acer->isHit()) {
            $acerFromDb = 'acer laptop';
            $acer->set($acerFromDb);
            $acer->tag(['computers','laptops','acer']);
            $cache->save($acer);
            dump('acer laptop from db');
        }

        if (!$dell->isHit()) {
            $dellFromDb = 'dell laptop';
            $dell->set($dellFromDb);
            $dell->tag(['computers','laptops','dell']);
            $cache->save($dell);
            dump('dell laptop from db');
        }

        if (!$ibm->isHit()) {
            $ibmFromDb = 'ibm desktop';
            $ibm->set($ibmFromDb);
            $ibm->tag(['computers','desktops','ibm']);
            $cache->save($ibm);
            dump('ibm desktop from db');
        }

        if (!$apple->isHit()) {
            $appleFromDb = 'apple desktop';
            $apple->set($appleFromDb);
            $apple->tag(['computers','desktops','apple']);
            $cache->save($apple);
            dump('apple desktop from db');
        }

//        $cache->invalidateTags(['ibm']);//1
//        $cache->invalidateTags(['desktops']);//2
//        $cache->invalidateTags(['laptops']);//3
        $cache->invalidateTags(['computers']);//4

        dump($acer, $dell, $ibm, $apple);

        return $this->render('default/relation.html.twig', ['status' => $status]);
    }

    /**
     * @Route("/listener", name="listener")
     */
    public function eventListenerAction()
    {
        $status = 'ОК';
        return $this->render('default/relation.html.twig', ['status' => $status]);
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

<?php

namespace App\Controller;

use App\Entity\Address;
use App\Entity\SecurityUser;
use App\Entity\User;
use App\Entity\Video;
use App\Event\VideoCreatedEvent;
use App\Form\RegisterUserType;
use App\Form\VideoFormType;
use App\Services\ServiceInterface;
use DateTimeImmutable;
use Doctrine\DBAL\Connection;
use Exception;
use Psr\Cache\CacheException;
use Psr\Cache\InvalidArgumentException;
use Psr\Log\LoggerInterface;
use Swift_Mailer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Component\Cache\Adapter\TagAwareAdapter;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Contracts\Cache\ItemInterface;

class DefaultController extends AbstractController
{
    /**
     * @var EventDispatcherInterface
     */
    private $dispatcher;
    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(EventDispatcherInterface $dispatcher, LoggerInterface $logger)
    {
        $this->logger = $logger;
        $this->dispatcher = $dispatcher;
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
     * @param Request $request
     * @return Response
     * @throws Exception
     */
    public function eventListenerAction(Request $request)
    {
        dump($request->request);
        $em = $this->getDoctrine()->getManager();
        $videos = $this->getDoctrine()->getRepository(Video::class)->findAll();
        dump($videos);
        $status = 'ОК';
        $video = new Video();
//        $video = $this->getDoctrine()->getRepository(Video::class)->findOneBy([]);
        $form = $this->createForm(VideoFormType::class, $video);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            dump($form->getData());
            $file = $form->get('file')->getData();
            dump($file);
            $fileName = sha1(random_bytes(14)) . '.' . $file->guessExtension();
            $file->move(
                $this->getParameter('image_directory'),
                $fileName
            );
            $video->setFile($fileName);
            $em->persist($video);
            $em->flush();
        }

        return $this->render('default/relation.html.twig', ['status' => $status, 'form' => $form->createView()]);
    }

    /**
     * @param Swift_Mailer $mailer
     * @Route("/mailing")
     * @return Response
     */
    public function mailingAction(Swift_Mailer $mailer)
    {
        $message = (new \Swift_Message('Hello Email'))
            ->setFrom('send@example.com')
            ->setTo('recipient@example.com')
            ->setBody(
                $this->renderView('emails/registration.html.twig', ['name'=>'Robert']),
                'text/html'
            );
        $mailer->send($message);

        return $this->render('default/mailing.html.twig', ['status'=>'OK']);
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

    /**
     * @param Request $request
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @return Response
     * @Route("/registration_form", name="registration")
     */
    public function registrationFormAction(Request $request, UserPasswordEncoderInterface $passwordEncoder)
    {
        $user = new SecurityUser();
        $form = $this->createForm(RegisterUserType::class, $user);

        $em =  $this->getDoctrine()->getManager();
        $users = $em->getRepository(SecurityUser::class)->findAll();

        dump($users);

        $form->handleREquest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            dump($form->get('password')->getData());
            $user->setPassword(
                $passwordEncoder->encodePassword($user, $form->get('password')->getData())
            );
            $user->setEmail($form->get('email')->getData());

            $em->persist($user);
            $em->flush();
        }

        return $this->render('default/registration_form.html.twig', [
            'status' => 'OK',
            'form' => $form->createView()
        ]);
    }

    /**
     * @param AuthenticationUtils $authenticationUtils
     * @return Response
     * @Route("/login", name="login")
     */
    public function login(AuthenticationUtils $authenticationUtils)
    {
        $error =  $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error,
        ]);
    }

    /**
     * @param Request $request
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @return Response
     * @throws Exception
     * @Route("create_admin_action")
     */
    public function createAdminAction(Request $request, UserPasswordEncoderInterface $passwordEncoder)
    {
        $em = $this->getDoctrine()->getManager();
        $users = $this->getDoctrine()->getRepository(SecurityUser::class)->findAll();
        dump($users);

        $user = new SecurityUser();
        $user->setEmail('admin1@email.ru');
        $user->setPassword(
            $passwordEncoder->encodePassword($user, '1234')
        );
        $user->setRoles(['ROLE_ADMIN']);

        $video = new Video();
        $video->setTitle('video title');
        $video->setFile('video path');
        $video->setCreatedAt(new \DateTime());
        $em->persist($video);

        $user->addVideo($video);

        $em->persist($user);
        $em->flush();

        dump($user->getId());
        dump($video->getId());

        return $this->render('default/status.html.twig', ['status' => 'ok']);
    }

}

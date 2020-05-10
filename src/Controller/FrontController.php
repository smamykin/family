<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\Comment;
use App\Entity\User;
use App\Entity\Video;
use App\Form\UserType;
use App\Repository\VideoRepository;
use App\Utils\CategoryTreeFrontPage;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class FrontController extends AbstractController
{
    /**
     * @Route("/", name="main_page")
     */
    public function index()
    {
        return $this->render('front/index.html.twig');
    }

    /**
     * @Route("/video-list/{categoryName},{id}/{page}", defaults={"page": "1"}, name="video_list")
     * @param $id
     * @param $page
     * @param CategoryTreeFrontPage $categoryTreeFrontPage
     * @param Request $request
     * @return Response
     */
    public function videoList($id, $page, CategoryTreeFrontPage $categoryTreeFrontPage, Request $request)
    {
        $categoryTreeFrontPage->getCategoryListAndParent($id);
        $ids = $categoryTreeFrontPage->getChildIds($id);
        array_push($ids, $id);

        $videos = $this->getDoctrine()
            ->getRepository(Video::class)
            ->findByChildIds($ids, $page, $request->get('sortby'));

        return $this->render('front/video_list.html.twig', [
            'subcategories' => $categoryTreeFrontPage,
            'videos' => $videos
        ]);
    }

    /**
     * @Route("/video-details/{video}", name="video_details")
     * @param VideoRepository $repo
     * @param Video $video
     * @return Response
     */
    public function videoDetails(VideoRepository $repo, $video)
    {
        return $this->render('front/video_details.html.twig', [
            'video'=> $repo->videoDetails($video),
        ]);
    }

    /**
     * @Route("/search-results/{page}", methods={"GET"}, defaults={"page": "1"}, name="search_results")
     * @param $page
     * @param Request $request
     * @return Response
     */
    public function searchResults($page, Request $request)
    {
        $videos = null;
        $query = null;
        if ($query = $request->get('query'))
        {
            $videos = $this->getDoctrine()
                ->getRepository(Video::class)
                ->findByTitle($query, $page, $request->get('sortby'));
            if (!$videos->getItems()) $videos = null;
        }
        return $this->render('front/search_results.html.twig', [
            'videos' => $videos,
            'query' => $query,

        ]);
    }

    /**
     * @Route("/pricing", name="pricing")
     */
    public function pricing()
    {
        return $this->render('front/pricing.html.twig');
    }

    /**
     * @Route("/register", name="register")
     * @param Request $request
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @return Response
     */
    public function register(Request $request, UserPasswordEncoderInterface $passwordEncoder)
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $formData = $request->request->get('user');
            $plainPassword = $formData['password']['first'];
            $user->setName($formData['name'])
                ->setLastName($formData['last_name'])
                ->setEmail($formData['email'])
                ->setPassword(
                    $passwordEncoder->encodePassword($user, $plainPassword)
                )
                ->setRoles(['ROLE_USER'])
            ;

            $objectManager = $this->getDoctrine()->getManager();
            $objectManager->persist($user);
            $objectManager->flush();

            $this->loginUserAutomatically($user, $plainPassword);

            return $this->redirectToRoute('admin_main_page');
        }
        return $this->render('front/register.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/login", name="login")
     * @param AuthenticationUtils $helper
     * @return Response
     */
    public function login(AuthenticationUtils $helper)
    {
        return $this->render('front/login.html.twig', [
            'error' => $helper->getLastAuthenticationError()
        ]);
    }

    /**
     * @Route("/logout", name="logout")
     * @throws Exception
     */
    public function logout(): void
    {
        throw new Exception('This should never be reached!');
    }

    /**
     * @Route("/payment", name="payment")
     */
    public function payment()
    {
        return $this->render('front/payment.html.twig');
    }

    public function mainCategories()
    {
        $categories = $this->getDoctrine()
            ->getRepository(Category::class)
            ->findBy(['parent'=>null], ['name' => 'ASC']);

        return $this->render('front/_main_categories.html.twig', [
            'categories' => $categories
        ]);
    }

    /**
     * @Route("/new-comment/{video}", methods={"POST"}, name="new_comment")
     * @param Video $video
     * @param Request $request
     * @return RedirectResponse
     */
    public function createComment(Video $video, Request $request)
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_REMEMBERED');
        $content = $request->request->get('comment');
        if (!empty(trim($content))) {
            $comment = new Comment();
            $comment->setContent($content)
                ->setUser($this->getUser())
                ->setVideo($video)
            ;

            $em = $this->getDoctrine()->getManager();
            $em->persist($comment);
            $em->flush();
        }

        return $this->redirectToRoute('video_details', [
            'video' => $video->getId(),
        ]);
    }

    /**
     * @Route("/video-list/{video}/like", name="like_video", methods={"POST"})
     * @Route("/video-list/{video}/dislike", name="dislike_video", methods={"POST"})
     * @Route("/video-list/{video}/unlike", name="undo_like_video", methods={"POST"})
     * @Route("/video-list/{video}/undodislike", name="undo_dislike_video", methods={"POST"})
     * @param Video $video
     * @param Request $request
     * @return JsonResponse
     */
    public function toggleLikesAjax(Video $video, Request $request)
    {

        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_REMEMBERED');

        switch($request->get('_route'))
        {
            case 'like_video':
                $result = $this->likeVideo($video);
                break;

            case 'dislike_video':
                $result = $this->dislikeVideo($video);
                break;

            case 'undo_like_video':
                $result = $this->undoLikeVideo($video);
                break;

            case 'undo_dislike_video':
                $result = $this->undoDislikeVideo($video);
                break;
        }

        return $this->json(['action' => $result,'id'=>$video->getId()]);
    }

    private function likeVideo($video)
    {
        $user = $this->getDoctrine()->getRepository(User::class)->find($this->getUser());
        $user->addLikedVideo($video);

        $em = $this->getDoctrine()->getManager();
        $em->persist($user);
        $em->flush();
        return 'liked';
    }
    private function dislikeVideo($video)
    {
        $user = $this->getDoctrine()->getRepository(User::class)->find($this->getUser());
        $user->addDislikedVideo($video);

        $em = $this->getDoctrine()->getManager();
        $em->persist($user);
        $em->flush();
        return 'disliked';
    }
    private function undoLikeVideo($video)
    {
        $user = $this->getDoctrine()->getRepository(User::class)->find($this->getUser());
        $user->removeLikedVideo($video);

        $em = $this->getDoctrine()->getManager();
        $em->persist($user);
        $em->flush();
        return 'undo liked';
    }
    private function undoDislikeVideo($video)
    {
        $user = $this->getDoctrine()->getRepository(User::class)->find($this->getUser());
        $user->removeDislikedVideo($video);

        $em = $this->getDoctrine()->getManager();
        $em->persist($user);
        $em->flush();
        return 'undo disliked';
    }

    private function loginUserAutomatically(User $user, $password)
    {
        $token = new UsernamePasswordToken($user, $password, 'main', $user->getRoles());
        $this->get('security.token_storage')->setToken($token);
        $this->get('session')->set('_security_main', serialize($token));

    }
}

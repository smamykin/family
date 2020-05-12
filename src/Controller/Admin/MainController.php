<?php

namespace App\Controller\Admin;

use App\Entity\Category;
use App\Entity\User;
use App\Entity\Video;
use App\Form\UserType;
use App\Utils\CategoryTreeAdminOptionList;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * @Route("/admin")
 */
class MainController extends AbstractController
{
    /**
     * @Route("/", name="admin_main_page")
     * @param Request $request
     * @param UserPasswordEncoderInterface $password_encoder
     * @return Response
     */
    public function index(Request $request, UserPasswordEncoderInterface $password_encoder)
    {
        /** @var User $user */
        $user = $this->getUser();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);
        $is_invalid = null;
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $user->setName($request->request->get('user')['name']);
            $user->setLastName($request->request->get('user')['last_name']);
            $user->setEmail($request->request->get('user')['email']);
            $password = $password_encoder->encodePassword($user, $request->request->get('user')['password']['first']);
            $user->setPassword($password);
            $entityManager->persist($user);
            $entityManager->flush();

            $this->addFlash(
                'success',
                'Your changes were saved!'
            );
            return $this->redirectToRoute('admin_main_page');
        } elseif ($request->isMethod('post')) {
            $is_invalid = 'is-invalid';
        }

        return $this->render('admin/my_profile.html.twig', [
            'subscription' => $this->getUser()->getSubscription(),
            'form' =>$form->createView(),
            'is_invalid' => $is_invalid,
        ]);
    }

    /**
     * @Route("/videos", name="videos")
     */
    public function videos()
    {
        if ($this->isGranted('ROLE_ADMIN')) {
            $videos = $this->getDoctrine()->getRepository(Video::class)->findAll();
        } else {
            $videos = $this->getUser()->getLikedVideos();
        }
        return $this->render('admin/videos.html.twig', [
            'videos' => $videos,
        ]);
    }

    public function getAllCategories(
        CategoryTreeAdminOptionList $categoryTreeAdminOptionList,
        Category $editedCategory = null
    ) {

        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        $categoryTreeAdminOptionList->getCategoryList($categoryTreeAdminOptionList->buildTree());
        return $this->render(
            'admin/_all_categories.html.twig',
            [
                'categories' => $categoryTreeAdminOptionList->categorylist,
                'editedCategory' => $editedCategory,
            ]
        );
    }

    /**
     * @Route("cancel-plan", name="cancel_plan")
     */
    public function cancelPlan()
    {
        /** @var User $user */
        $user = $this->getDoctrine()->getRepository(User::class)->find($this->getUser());
        $subscription = $user->getSubscription();
        $subscription->setValidTo(new DateTime());
        $subscription->setPaymentStatus(null);
        $subscription->setPlan('canceled');

        $em = $this->getDoctrine()->getManager();
        $em->persist($user);
        $em->persist($subscription);
        $em->flush();

        return $this->redirectToRoute('admin_main_page');
    }

    /**
     * @Route("delete-accaunt", name="delete_account")
     */
    public function deleteAccount()
    {
        $em = $this->getDoctrine()->getManager();
        /** @var User $user */
        $user = $em->getRepository(User::class)->find($this->getUser());

        $em->remove($user);
        $em->flush();

        session_destroy();
    }
}

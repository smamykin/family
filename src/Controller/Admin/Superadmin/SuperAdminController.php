<?php

namespace App\Controller\Admin\Superadmin;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin/su")
 */
class SuperAdminController extends AbstractController
{
    /**
     * @Route("/upload-video", name="upload_video")
     */
    public function upload_video()
    {
        return $this->render('admin/upload_video.html.twig');
    }

    /**
     * @Route("/users", name="users")
     */
    public function users()
    {
        $rep = $this->getDoctrine()->getRepository(User::class);
        $users = $rep->findBy([], ['name' => 'ASC']);
        return $this->render('admin/users.html.twig', ['users' => $users]);
    }

    /**
     * @Route("/delete-user/{user}", name="delete_user")
     * @param User $user
     * @return RedirectResponse
     */
    public function deleteUser(User $user)
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($user);
        $em->flush();

        return $this->redirectToRoute('users');
    }
}

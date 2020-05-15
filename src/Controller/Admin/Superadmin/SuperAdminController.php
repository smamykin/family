<?php

namespace App\Controller\Admin\Superadmin;

use App\Entity\Category;
use App\Entity\User;
use App\Entity\Video;
use App\Form\VideoType;
use App\Utils\Interfaces\UploaderInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin/su")
 */
class SuperAdminController extends AbstractController
{
    /**
     * @Route("/upload-video-locally", name="upload_video_locally")
     * @param Request $request
     * @param UploaderInterface $fileUploader
     * @return Response
     */
    public function uploadVideoLocally(Request $request, UploaderInterface $fileUploader)
    {
        $video =  new Video();
        $form = $this->createForm(VideoType::class, $video);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            $file = $video->getUploadedVideo();
            $fileName = $fileUploader->upload($file);

            $base_path = Video::uploadFolder;
            $video->setPath($base_path.$fileName[0]);
            $video->setTitle($fileName[1]);

            $em->persist($video);
            $em->flush();

            return $this->redirectToRoute('videos');
        }
        return $this->render('admin/upload_video_locally.html.twig',[
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/delete-video/{video}/{path}", name="delete_video", requirements={"path"=".+"})
     * @param Video $video
     * @param $path
     * @param UploaderInterface $fileUploader
     * @return RedirectResponse
     */
    public function deleteVideo(Video $video, $path, UploaderInterface $fileUploader)
    {

        $em = $this->getDoctrine()->getManager();
        $em->remove($video);
        $em->flush();

        if ($fileUploader->delete($path)) {
            $this->addFlash(
                'success',
                'The video was successfully deleted.'
            );
        } else {
            $this->addFlash(
                'danger',
                'We were not able to delete. Check the video.'
            );
        }

        return $this->redirectToRoute('videos');

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

    /**
     * @Route("/update-video-category/{video}", methods={"POST"}, name="update_video_category")
     * @param Request $request
     * @param Video $video
     * @return RedirectResponse
     */
    public function updateVideoCategory(Request $request, Video $video)
    {
        $em = $this->getDoctrine()->getManager();

        /** @var Category $category */
        $category = $this->getDoctrine()->getRepository(Category::class)->find($request->request->get('video_category'));

        $video->setCategory($category);

        $em->persist($video);
        $em->flush();

        return $this->redirectToRoute('videos');
    }

}

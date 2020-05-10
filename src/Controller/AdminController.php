<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\Video;
use App\Form\CategoryType;
use App\Utils\CategoryTreeAdminList;
use App\Utils\CategoryTreeAdminOptionList;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin")
 */
class AdminController extends AbstractController
{
    /**
     * @Route("/", name="admin_main_page")
     */
    public function index()
    {
        return $this->render('admin/my_profile.html.twig');
    }

    /**
     * @Route("/su/categories", name="categories", methods={"GET", "POST"})
     * @param CategoryTreeAdminList $categoryTreeAdminList
     * @param Request $request
     * @return Response
     */
    public function categories(CategoryTreeAdminList $categoryTreeAdminList, Request $request)
    {
        $categoryTreeAdminList->getCategoryList($categoryTreeAdminList->buildTree());

        $category = new Category();
        $form = $this->createForm(CategoryType::class, $category);

        $isInvalid = false;
        if ($this->isSaveCategory($form, $request, $category)) {
            return $this->redirectToRoute('categories');
        } elseif($request->isMethod('post')) {
            $isInvalid = true;
        }

        return $this->render('admin/categories.html.twig', [
            'categories' => $categoryTreeAdminList->categorylist,
            'form' => $form->createView(),
            'is_invalid' => $isInvalid,
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

    /**
     * @Route("/su/upload-video", name="upload_video")
     */
    public function upload_video()
    {
        return $this->render('admin/upload_video.html.twig');
    }

    /**
     * @Route("/su/users", name="users")
     */
    public function users()
    {
        return $this->render('admin/users.html.twig');
    }

    /**
     * @Route("/su/edit_category/{id}", name="edit_category")
     * @param Category $category
     * @param Request $request
     * @return Response
     */
    public function editCategory(Category $category, Request $request)
    {
        $form = $this->createForm(CategoryType::class, $category);

        $isInvalid = false;
        if ($this->isSaveCategory($form, $request, $category)) {
            return $this->redirectToRoute('categories');
        } elseif($request->isMethod('post')) {
            $isInvalid = true;
        }

        return $this->render('admin/edit_category.html.twig', [
            'category' => $category,
            'form' => $form->createView(),
            'is_invalid' => $isInvalid,
        ]);
    }

    /**
     * @Route("/su/delete_category/{id}", name="delete_category")
     * @param Category $category
     * @return Response
     */
    public function deleteCategory(Category $category)
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($category);
        $em->flush();
        return $this->redirectToRoute('categories');
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
     * @param Request $request
     * @param Category $category
     */
    public function saveCategory(Request $request, Category $category) {
        $post = $request->request->get('category');
        $category->setName($post['name']);
        if ($post['parent']) {
            $repository = $this->getDoctrine()->getRepository(Category::class);
            /** @var Category $parent */
            $parent = $repository->find($post['parent']);
            $category->setParent($parent);
        }
        $em = $this->getDoctrine()->getManager();
        $em->persist($category);
        $em->flush();

    }

    private function isSaveCategory(FormInterface $form, Request $request, Category $category) {

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->saveCategory($request, $category);
            return true;
        }
        return false;

    }
}

<?php

namespace App\Controller;

use App\Entity\Category;
use App\Form\CategoryType;
use App\Utils\CategoryTreeAdminList;
use App\Utils\CategoryTreeAdminOptionList;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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
     * @Route("/categories", name="categories", methods={"GET", "POST"})
     * @param CategoryTreeAdminList $categoryTreeAdminList
     * @param Request $request
     * @return Response
     */
    public function categories(CategoryTreeAdminList $categoryTreeAdminList, Request $request)
    {
        $categoryTreeAdminList->getCategoryList($categoryTreeAdminList->buildTree());

        $category = new Category();
        $form = $this->createForm(CategoryType::class, $category);

        $form->handleRequest($request);

        $isInvalid = false;
        if ($form->isSubmitted() && $form->isValid()) {
            dd('valid');
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
        return $this->render('admin/videos.html.twig');
    }

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
        return $this->render('admin/users.html.twig');
    }

    /**
     * @Route("/edit_category/{id}", name="edit_category")
     * @param Category $category
     * @return Response
     */
    public function editCategory(Category $category)
    {
        return $this->render('admin/edit_category.html.twig', [
            'category' => $category
        ]);
    }

    /**
     * @Route("/delete_category/{id}", name="delete_category")
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
        $categoryTreeAdminOptionList->getCategoryList($categoryTreeAdminOptionList->buildTree());
        return $this->render(
            'admin/_all_categories.html.twig',
            [
                'categories' => $categoryTreeAdminOptionList->categorylist,
                'editedCategory' => $editedCategory,
            ]
        );
    }
}

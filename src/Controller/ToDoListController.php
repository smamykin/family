<?php

namespace App\Controller;

use App\Entity\Task;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class ToDoListController extends AbstractController
{
    /**
     * @Route("/to/do/list", name="to_do_list")
     */
    public function index()
    {
        return $this->render('to_do_list/index.html.twig', [
            'controller_name' => 'ToDoListController',
        ]);
    }

    /**
     * @Route("/create", name="create_task", methods={"POST"})
     * @param Request $request
     */
    public function create(Request $request)
    {

        $title  = trim($request->request->get('task_name'));
        if (empty($title)) {
            return $this->redirectToRoute('to_do_list');
        }

        $em = $this->getDoctrine()->getManager();
        $task = new Task();
        $task->setTitle($title);

        $em->persist($task);
        $em->flush();

        return $this->redirectToRoute('to_do_list');
    }

    /**
     * @Route("/switch_status/{id}", name="switch_status")
     * @param int $id
     */
    public function switchStatus(int $id)
    {
        exit('switch hi hi hi - ' . $id);
    }

    /**
     * @Route("/delete/{id}", name="task_delete")
     * @param int $id
     */
    public function delete(int $id)
    {
        exit('delete hi hi hi - ' . $id);
    }
}

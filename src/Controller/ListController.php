<?php

namespace App\Controller;

use App\Entity\Task;
use App\Entity\TodoList;
use App\Form\TaskFormType;
use App\Repository\TaskRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ListController extends AbstractController
{
    public function __construct(
        private TaskRepository $taskRepository,
    ) {
    }

    #[Route('/list/{id}', name: 'app_list', methods: ['GET', 'POST'])]
    public function index(TodoList $todolist, Request $request): Response
    {
        $task = new Task();
        $form = $this->createForm(TaskFormType::class, $task);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            $this->taskRepository->add($task, $todolist);
            return $this->redirectToRoute('app_list', ['id' => $todolist->getId()]);
        };

        return $this->render('list/index.html.twig', [
            'tasks' => $todolist->getTasks(),
            'todolist' => $todolist,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/task_remove', name: 'app_task_remove', methods: ['DELETE'])]
    public function taskDelete(Request $request): Response
    {
        $this->taskRepository->delete($request);
        return new Response(Response::HTTP_OK);
    }
}

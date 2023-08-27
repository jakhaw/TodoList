<?php

namespace App\Controller;

use App\Entity\Task;
use App\Entity\TodoList;
use App\Form\TaskFormType;
use App\Repository\TaskRepository;
use App\Repository\TodoListRepository;
use App\Service\FindEntity;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ListController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private TaskRepository $taskRepository,
        private TodoListRepository $todoListRepository,
    ) {
    }

    #[Route('/list/{id}', name: 'app_list', methods: ['GET', 'POST'])]
    public function index(TodoList $todolist, Request $request): Response
    {
        $task = new Task();
        $form = $this->createForm(TaskFormType::class, $task);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){

            $task->setTodolist($todolist);
            $this->entityManager->persist($task);
            $this->entityManager->flush();

            return $this->redirectToRoute('app_list', ['id' => $todolist->getId()]);
        };

        return $this->render('list/index.html.twig', [
            'tasks' => $todolist->getTasks(),
            'todolist' => $todolist,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/task_remove', name: 'app_task_remove', methods: ['DELETE'])]
    public function taskDelete(Request $request, FindEntity $findEntity): Response
    {
        $task = $findEntity->findEntityById($request->get('id'), $this->taskRepository);

        $this->entityManager->remove($task);
        $this->entityManager->flush();

        return new Response(Response::HTTP_OK);
    }
}

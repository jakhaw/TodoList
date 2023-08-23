<?php

namespace App\Controller;

use App\Entity\Task;
use App\Form\TaskFormType;
use App\Repository\TaskRepository;
use App\Repository\TodoListRepository;
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
    public function index($id, Request $request): Response
    {
        $todolist = $this->todoListRepository->findOneBy(['id' => $id]);
        $tasks = $todolist->getTasks();

        $task = new Task();
        $form = $this->createForm(TaskFormType::class, $task);

        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){

            $newTask = $form->getData();

            $newTask->setTodolist($todolist);

            $this->entityManager->persist($newTask);
            $this->entityManager->flush();

            return $this->redirectToRoute('app_list', ['id' => $id]);
        };

        return $this->render('list/index.html.twig', [
            'tasks' => $tasks,
            'todolist' => $todolist,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/task_remove', name: 'app_task_remove', methods: ['DELETE'])]
    public function taskDelete(Request $request): Response
    {
        $remove_task_id = $request->get('id');

        $task_to_remove = $this->taskRepository->findOneBy(['id' => $remove_task_id]);

        $this->entityManager->remove($task_to_remove);
        $this->entityManager->flush();

        return new Response(Response::HTTP_OK);
    }
}

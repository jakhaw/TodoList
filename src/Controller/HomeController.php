<?php

namespace App\Controller;

use App\Entity\TodoList;
use App\Form\TodolistFormType;
use App\Repository\TodoListRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    public function __construct(
        private TodoListRepository $todoListRepository,
    ) {
    }

    #[Route('/home', name: 'app_home', methods: ['GET', 'POST'])]
    public function index(Request $request): Response
    {
        $todolist = new TodoList();
        $form = $this->createForm(TodolistFormType::class, $todolist);
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $user = $this->getUser();
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            $this->todoListRepository->add($todolist, $user);
            return $this->redirectToRoute('app_home');
        };

        return $this->render('home/index.html.twig', [
            'form' => $form->createView(),
            'todolists' => $user->getTodolists(),
        ]);
    }

    #[Route('/todolist_remove', name: 'app_todolist_remove', methods: ['DELETE'])]
    public function todolistDelete(Request $request): Response
    {
        $this->todoListRepository->delete($request);
        return new Response(Response::HTTP_OK);
    }
}

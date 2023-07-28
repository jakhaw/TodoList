<?php

namespace App\Controller;

use App\Entity\TodoList;
use App\Form\TodolistFormType;
use App\Repository\TodoListRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

class HomeController extends AbstractController
{
    public function __construct(
        private Security $security,
        private EntityManagerInterface $entityManager,
        private TodoListRepository $todoListRepository,
    ) {
    }

    #[Route('/home', name: 'app_home')]
    public function index(Request $request): Response
    {
        $todolist = new TodoList();
        $form = $this->createForm(TodolistFormType::class, $todolist);
        $user = $this->security->getUser();
        $todolists = $user->getTodolists();

        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            
            $newTodoList = $form->getData();

            $newTodoList->setUser($user);

            $this->entityManager->persist($newTodoList);
            $this->entityManager->flush();

            return $this->redirectToRoute('app_home');
        };

        return $this->render('home/index.html.twig', [
            'form' => $form->createView(),
            'todolists' => $todolists,
        ]);
    }
}

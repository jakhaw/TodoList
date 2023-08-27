<?php

namespace App\Controller;

use App\Entity\TodoList;
use App\Form\TodolistFormType;
use App\Repository\TodoListRepository;
use App\Service\FindEntity;
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

    #[Route('/home', name: 'app_home', methods: ['GET', 'POST'])]
    public function index(Request $request): Response
    {
        $todolist = new TodoList();
        $form = $this->createForm(TodolistFormType::class, $todolist);
        
        $user = $this->security->getUser();

        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            
            $todolist->setUser($user);
            $this->entityManager->persist($todolist);
            $this->entityManager->flush();

            return $this->redirectToRoute('app_home');
        };

        return $this->render('home/index.html.twig', [
            'form' => $form->createView(),
            'todolists' => $user->getTodolists(),
        ]);
    }

    #[Route('/todolist_remove', name: 'app_todolist_remove', methods: ['DELETE'])]
    public function todolistDelete(Request $request, FindEntity $findEntity): Response
    {
        $todolist = $findEntity->findEntityById($request->get('id'), $this->todoListRepository);

        $this->entityManager->remove($todolist);
        $this->entityManager->flush();

        return new Response(Response::HTTP_OK);
    }
}

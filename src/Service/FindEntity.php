<?php

namespace App\Service;

use App\Entity\Task;
use App\Entity\TodoList;
use App\Repository\TaskRepository;
use App\Repository\TodoListRepository;

class FindEntity 
{
    public function findEntityById($id, TaskRepository|TodoListRepository $entityRepository): Task|TodoList
    {
        $entity = $entityRepository->findOneBy(['id' => $id]);

        return $entity;
    }
}
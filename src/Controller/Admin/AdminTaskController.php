<?php

namespace App\Controller\Admin;

use App\Entity\Task;
use App\Form\TaskType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin/tasks')]
class AdminTaskController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $entityManager
    ) {}

    #[Route('/', name: 'admin_tasks_index')]
    public function index(): Response
    {
        $tasks = $this->entityManager->getRepository(Task::class)->findAll();
        
        return $this->render('admin/tasks/index.html.twig', [
            'tasks' => $tasks,
        ]);
    }

    #[Route('/new', name: 'admin_tasks_new')]
    public function new(Request $request): Response
    {
        $task = new Task();
        $form = $this->createForm(TaskType::class, $task);
        
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $this->entityManager->persist($task);
                $this->entityManager->flush();
                
                $this->addFlash('success', 'Task created successfully!');
                return $this->redirectToRoute('admin_tasks_index');
            } catch (\Exception $e) {
                $this->addFlash('error', 'An error occurred while creating the task.');
            }
        }
        
        return $this->render('admin/tasks/new.html.twig', [
            'form' => $form->createView(),
            'task' => $task,
        ]);
    }

    #[Route('/{id}/edit', name: 'admin_tasks_edit')]
    public function edit(Task $task, Request $request): Response
    {
        $form = $this->createForm(TaskType::class, $task);
        
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $this->entityManager->flush();
                
                $this->addFlash('success', 'Task updated successfully!');
                return $this->redirectToRoute('admin_tasks_index');
            } catch (\Exception $e) {
                $this->addFlash('error', 'An error occurred while updating the task.');
            }
        }
        
        return $this->render('admin/tasks/edit.html.twig', [
            'form' => $form->createView(),
            'task' => $task,
        ]);
    }

    #[Route('/{id}/delete', name: 'admin_tasks_delete', methods: ['POST'])]
    public function delete(Task $task): Response
    {
        try {
            $this->entityManager->remove($task);
            $this->entityManager->flush();
            
            $this->addFlash('success', 'Task deleted successfully!');
        } catch (\Exception $e) {
            $this->addFlash('error', 'An error occurred while deleting the task.');
        }
        
        return $this->redirectToRoute('admin_tasks_index');
    }
}
<?php

namespace App\Controller\Admin;

use App\Entity\User;
use App\Form\UserType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin/users')]
class AdminUserController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $entityManager
    ) {}

    #[Route('/', name: 'admin_users_index')]
    public function index(): Response
    {
        $users = $this->entityManager->getRepository(User::class)->findAll();
        
        return $this->render('admin/users/index.html.twig', [
            'users' => $users,
        ]);
    }

    #[Route('/new', name: 'admin_users_new')]
    public function new(Request $request): Response
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $this->entityManager->persist($user);
                $this->entityManager->flush();
                
                $this->addFlash('success', 'User created successfully!');
                return $this->redirectToRoute('admin_users_index');
            } catch (\Exception $e) {
                if (str_contains($e->getMessage(), 'Duplicate entry')) {
                    $this->addFlash('error', 'Email already exists!');
                } else {
                    $this->addFlash('error', 'An error occurred while creating the user.');
                }
            }
        }
        
        return $this->render('admin/users/new.html.twig', [
            'form' => $form->createView(),
            'user' => $user,
        ]);
    }

    #[Route('/{id}/edit', name: 'admin_users_edit')]
    public function edit(User $user, Request $request): Response
    {
        $form = $this->createForm(UserType::class, $user);
        
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $this->entityManager->flush();
                
                $this->addFlash('success', 'User updated successfully!');
                return $this->redirectToRoute('admin_users_index');
            } catch (\Exception $e) {
                if (str_contains($e->getMessage(), 'Duplicate entry')) {
                    $this->addFlash('error', 'Email already exists!');
                } else {
                    $this->addFlash('error', 'An error occurred while updating the user.');
                }
            }
        }
        
        return $this->render('admin/users/edit.html.twig', [
            'form' => $form->createView(),
            'user' => $user,
        ]);
    }

    #[Route('/{id}/delete', name: 'admin_users_delete', methods: ['POST'])]
    public function delete(User $user): Response
    {
        try {
            $this->entityManager->remove($user);
            $this->entityManager->flush();
            
            $this->addFlash('success', 'User deleted successfully!');
        } catch (\Exception $e) {
            $this->addFlash('error', 'Cannot delete user with existing tasks.');
        }
        
        return $this->redirectToRoute('admin_users_index');
    }
}
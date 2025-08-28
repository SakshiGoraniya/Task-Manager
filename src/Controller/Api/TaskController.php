<?php

namespace App\Controller\Api;

use App\Dto\CreateTaskDto;
use App\Dto\UpdateTaskStatusDto;
use App\Entity\Task;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/api/tasks')]
class TaskController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private ValidatorInterface $validator,
        private SerializerInterface $serializer
    ) {}

    #[Route('', methods: ['GET'])]
    public function list(): JsonResponse
    {
        $tasks = $this->entityManager->getRepository(Task::class)->findAll();
        
        $data = array_map(fn($task) => [
            'id' => $task->getId(),
            'title' => $task->getTitle(),
            'description' => $task->getDescription(),
            'status' => $task->getStatus(),
            'user_id' => $task->getUser()->getId(),
            'user_name' => $task->getUser()->getName(),
            'created_at' => $task->getCreatedAt()->format('Y-m-d H:i:s'),
            'updated_at' => $task->getUpdatedAt()->format('Y-m-d H:i:s')
        ], $tasks);

        return new JsonResponse($data);
    }

    #[Route('', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        try {
            $dto = $this->serializer->deserialize($request->getContent(), CreateTaskDto::class, 'json');
        } catch (\Exception $e) {
            return new JsonResponse(['error' => 'Invalid JSON'], Response::HTTP_BAD_REQUEST);
        }

        $errors = $this->validator->validate($dto);
        if (count($errors) > 0) {
            $errorMessages = [];
            foreach ($errors as $error) {
                $errorMessages[$error->getPropertyPath()] = $error->getMessage();
            }
            return new JsonResponse(['errors' => $errorMessages], Response::HTTP_BAD_REQUEST);
        }

        $user = $this->entityManager->getRepository(User::class)->find($dto->user_id);
        if (!$user) {
            return new JsonResponse(['error' => 'User not found'], Response::HTTP_NOT_FOUND);
        }

        $task = new Task();
        $task->setTitle($dto->title);
        $task->setDescription($dto->description);
        $task->setUser($user);

        $this->entityManager->persist($task);
        $this->entityManager->flush();

        return new JsonResponse([
            'id' => $task->getId(),
            'title' => $task->getTitle(),
            'description' => $task->getDescription(),
            'status' => $task->getStatus(),
            'user_id' => $task->getUser()->getId()
        ], Response::HTTP_CREATED);
    }

    #[Route('/{id}', methods: ['GET'])]
    public function show(Task $task): JsonResponse
    {
        return new JsonResponse([
            'id' => $task->getId(),
            'title' => $task->getTitle(),
            'description' => $task->getDescription(),
            'status' => $task->getStatus(),
            'user_id' => $task->getUser()->getId(),
            'user_name' => $task->getUser()->getName(),
            'created_at' => $task->getCreatedAt()->format('Y-m-d H:i:s'),
            'updated_at' => $task->getUpdatedAt()->format('Y-m-d H:i:s')
        ]);
    }

    #[Route('/{id}', methods: ['PUT'])]
    public function update(Task $task, Request $request): JsonResponse
    {
        try {
            $dto = $this->serializer->deserialize($request->getContent(), CreateTaskDto::class, 'json');
        } catch (\Exception $e) {
            return new JsonResponse(['error' => 'Invalid JSON'], Response::HTTP_BAD_REQUEST);
        }

        $errors = $this->validator->validate($dto);
        if (count($errors) > 0) {
            $errorMessages = [];
            foreach ($errors as $error) {
                $errorMessages[$error->getPropertyPath()] = $error->getMessage();
            }
            return new JsonResponse(['errors' => $errorMessages], Response::HTTP_BAD_REQUEST);
        }

        $user = $this->entityManager->getRepository(User::class)->find($dto->user_id);
        if (!$user) {
            return new JsonResponse(['error' => 'User not found'], Response::HTTP_NOT_FOUND);
        }

        $task->setTitle($dto->title);
        $task->setDescription($dto->description);
        $task->setUser($user);

        $this->entityManager->flush();

        return new JsonResponse([
            'id' => $task->getId(),
            'title' => $task->getTitle(),
            'description' => $task->getDescription(),
            'status' => $task->getStatus(),
            'user_id' => $task->getUser()->getId(),
            'user_name' => $task->getUser()->getName(),
            'created_at' => $task->getCreatedAt()->format('Y-m-d H:i:s'),
            'updated_at' => $task->getUpdatedAt()->format('Y-m-d H:i:s')
        ]);
    }

    #[Route('/user/{user}', methods: ['GET'])]
    public function listByUser(User $user): JsonResponse
    {
        $tasks = $this->entityManager->getRepository(Task::class)->findBy(['user' => $user]);
        
        $data = array_map(fn($task) => [
            'id' => $task->getId(),
            'title' => $task->getTitle(),
            'description' => $task->getDescription(),
            'status' => $task->getStatus(),
            'created_at' => $task->getCreatedAt()->format('Y-m-d H:i:s')
        ], $tasks);

        return new JsonResponse($data);
    }

    #[Route('/{task}/status', methods: ['PUT'])]
    public function updateStatus(Task $task, Request $request): JsonResponse
    {
        try {
            $dto = $this->serializer->deserialize($request->getContent(), UpdateTaskStatusDto::class, 'json');
        } catch (\Exception $e) {
            return new JsonResponse(['error' => 'Invalid JSON'], Response::HTTP_BAD_REQUEST);
        }

        $errors = $this->validator->validate($dto);
        if (count($errors) > 0) {
            $errorMessages = [];
            foreach ($errors as $error) {
                $errorMessages[$error->getPropertyPath()] = $error->getMessage();
            }
            return new JsonResponse(['errors' => $errorMessages], Response::HTTP_BAD_REQUEST);
        }

        $task->setStatus($dto->status);

        $this->entityManager->flush();

        return new JsonResponse([
            'id' => $task->getId(),
            'status' => $task->getStatus()
        ]);
    }

    #[Route('/{task}', methods: ['DELETE'])]
    public function delete(Task $task): JsonResponse
    {

        $this->entityManager->remove($task);
        $this->entityManager->flush();

        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }
}
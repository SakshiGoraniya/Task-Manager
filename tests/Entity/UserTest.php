<?php

namespace App\Tests\Entity;

use App\Entity\Task;
use App\Entity\User;
use PHPUnit\Framework\TestCase;

class UserTest extends TestCase
{
    private User $user;

    protected function setUp(): void
    {
        $this->user = new User();
    }

    public function testUserCreation(): void
    {
        $this->assertInstanceOf(User::class, $this->user);
        $this->assertNull($this->user->getId());
        $this->assertCount(0, $this->user->getTasks());
    }

    public function testSetAndGetName(): void
    {
        $name = 'John Doe';
        
        $result = $this->user->setName($name);
        
        $this->assertSame($this->user, $result);
        $this->assertSame($name, $this->user->getName());
    }

    public function testSetAndGetEmail(): void
    {
        $email = 'john.doe@example.com';
        
        $result = $this->user->setEmail($email);
        
        $this->assertSame($this->user, $result);
        $this->assertSame($email, $this->user->getEmail());
    }

    public function testAddTask(): void
    {
        $task = new Task();
        
        $result = $this->user->addTask($task);
        
        $this->assertSame($this->user, $result);
        $this->assertTrue($this->user->getTasks()->contains($task));
        $this->assertSame($this->user, $task->getUser());
        $this->assertCount(1, $this->user->getTasks());
    }

    public function testAddTaskTwice(): void
    {
        $task = new Task();
        
        $this->user->addTask($task);
        $this->user->addTask($task);
        
        $this->assertCount(1, $this->user->getTasks());
        $this->assertTrue($this->user->getTasks()->contains($task));
    }

    public function testRemoveTask(): void
    {
        $task = new Task();
        $this->user->addTask($task);
        
        $result = $this->user->removeTask($task);
        
        $this->assertSame($this->user, $result);
        $this->assertFalse($this->user->getTasks()->contains($task));
        $this->assertNull($task->getUser());
        $this->assertCount(0, $this->user->getTasks());
    }

    public function testRemoveTaskNotInCollection(): void
    {
        $task = new Task();
        
        $result = $this->user->removeTask($task);
        
        $this->assertSame($this->user, $result);
        $this->assertCount(0, $this->user->getTasks());
    }

    public function testRemoveTaskWithDifferentUser(): void
    {
        $task = new Task();
        $otherUser = new User();
        $task->setUser($otherUser);
        $this->user->getTasks()->add($task);
        
        $this->user->removeTask($task);
        
        $this->assertSame($otherUser, $task->getUser());
    }

    public function testFluentInterface(): void
    {
        $name = 'Jane Smith';
        $email = 'jane.smith@example.com';

        $result = $this->user
            ->setName($name)
            ->setEmail($email);

        $this->assertSame($this->user, $result);
        $this->assertSame($name, $this->user->getName());
        $this->assertSame($email, $this->user->getEmail());
    }

    public function testTasksCollection(): void
    {
        $task1 = new Task();
        $task2 = new Task();
        $task3 = new Task();

        $this->user
            ->addTask($task1)
            ->addTask($task2)
            ->addTask($task3);

        $tasks = $this->user->getTasks();
        
        $this->assertCount(3, $tasks);
        $this->assertTrue($tasks->contains($task1));
        $this->assertTrue($tasks->contains($task2));
        $this->assertTrue($tasks->contains($task3));

        $this->user->removeTask($task2);
        
        $this->assertCount(2, $tasks);
        $this->assertTrue($tasks->contains($task1));
        $this->assertFalse($tasks->contains($task2));
        $this->assertTrue($tasks->contains($task3));
    }
}
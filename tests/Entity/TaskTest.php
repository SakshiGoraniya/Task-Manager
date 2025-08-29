<?php

namespace App\Tests\Entity;

use App\Entity\Task;
use App\Entity\User;
use PHPUnit\Framework\TestCase;

class TaskTest extends TestCase
{
    private Task $task;

    protected function setUp(): void
    {
        $this->task = new Task();
    }

    public function testTaskCreation(): void
    {
        $this->assertInstanceOf(Task::class, $this->task);
        $this->assertNull($this->task->getId());
        $this->assertSame(Task::STATUS_TODO, $this->task->getStatus());
    }

    public function testSetAndGetTitle(): void
    {
        $title = 'Test Task Title';
        
        $result = $this->task->setTitle($title);
        
        $this->assertSame($this->task, $result);
        $this->assertSame($title, $this->task->getTitle());
    }

    public function testSetAndGetDescription(): void
    {
        $description = 'This is a test task description';
        
        $result = $this->task->setDescription($description);
        
        $this->assertSame($this->task, $result);
        $this->assertSame($description, $this->task->getDescription());
    }

    public function testSetAndGetStatus(): void
    {
        $status = Task::STATUS_IN_PROGRESS;
        
        $result = $this->task->setStatus($status);
        
        $this->assertSame($this->task, $result);
        $this->assertSame($status, $this->task->getStatus());
    }

    public function testSetAndGetUser(): void
    {
        $user = new User();
        
        $result = $this->task->setUser($user);
        
        $this->assertSame($this->task, $result);
        $this->assertSame($user, $this->task->getUser());
    }

    public function testSetAndGetCreatedAt(): void
    {
        $date = new \DateTime('2024-01-01 10:00:00');
        
        $result = $this->task->setCreatedAt($date);
        
        $this->assertSame($this->task, $result);
        $this->assertSame($date, $this->task->getCreatedAt());
    }

    public function testSetAndGetUpdatedAt(): void
    {
        $date = new \DateTime('2024-01-01 11:00:00');
        
        $result = $this->task->setUpdatedAt($date);
        
        $this->assertSame($this->task, $result);
        $this->assertSame($date, $this->task->getUpdatedAt());
    }

    public function testSetCreatedAtValue(): void
    {
        $beforeCall = new \DateTime();
        
        $this->task->setCreatedAtValue();
        
        $afterCall = new \DateTime();
        
        $this->assertInstanceOf(\DateTime::class, $this->task->getCreatedAt());
        $this->assertInstanceOf(\DateTime::class, $this->task->getUpdatedAt());
        $this->assertGreaterThanOrEqual($beforeCall, $this->task->getCreatedAt());
        $this->assertLessThanOrEqual($afterCall, $this->task->getCreatedAt());
        $this->assertGreaterThanOrEqual($beforeCall, $this->task->getUpdatedAt());
        $this->assertLessThanOrEqual($afterCall, $this->task->getUpdatedAt());
    }

    public function testSetUpdatedAtValue(): void
    {
        $initialDate = new \DateTime('2024-01-01 10:00:00');
        $this->task->setUpdatedAt($initialDate);
        
        $beforeCall = new \DateTime();
        $this->task->setUpdatedAtValue();
        $afterCall = new \DateTime();
        
        $this->assertInstanceOf(\DateTime::class, $this->task->getUpdatedAt());
        $this->assertGreaterThanOrEqual($beforeCall, $this->task->getUpdatedAt());
        $this->assertLessThanOrEqual($afterCall, $this->task->getUpdatedAt());
    }

    public function testGetStatusChoices(): void
    {
        $expected = [
            'To Do' => Task::STATUS_TODO,
            'In Progress' => Task::STATUS_IN_PROGRESS,
            'Done' => Task::STATUS_DONE,
        ];
        
        $this->assertSame($expected, Task::getStatusChoices());
    }

    public function testStatusConstants(): void
    {
        $this->assertSame('todo', Task::STATUS_TODO);
        $this->assertSame('in_progress', Task::STATUS_IN_PROGRESS);
        $this->assertSame('done', Task::STATUS_DONE);
    }

    public function testFluentInterface(): void
    {
        $user = new User();
        $title = 'Fluent Test';
        $description = 'Testing fluent interface';
        $status = Task::STATUS_DONE;
        $createdAt = new \DateTime();
        $updatedAt = new \DateTime();

        $result = $this->task
            ->setTitle($title)
            ->setDescription($description)
            ->setStatus($status)
            ->setUser($user)
            ->setCreatedAt($createdAt)
            ->setUpdatedAt($updatedAt);

        $this->assertSame($this->task, $result);
        $this->assertSame($title, $this->task->getTitle());
        $this->assertSame($description, $this->task->getDescription());
        $this->assertSame($status, $this->task->getStatus());
        $this->assertSame($user, $this->task->getUser());
        $this->assertSame($createdAt, $this->task->getCreatedAt());
        $this->assertSame($updatedAt, $this->task->getUpdatedAt());
    }
}
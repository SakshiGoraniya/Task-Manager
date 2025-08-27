<?php

namespace App\Dto;

use Symfony\Component\Validator\Constraints as Assert;

class UpdateTaskStatusDto
{
    #[Assert\NotBlank(message: 'Status is required')]
    #[Assert\Choice(choices: ['todo', 'in_progress', 'done'], message: 'Status must be one of: todo, in_progress, done')]
    public string $status;
}
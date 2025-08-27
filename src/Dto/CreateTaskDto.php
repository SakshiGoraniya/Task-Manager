<?php

namespace App\Dto;

use Symfony\Component\Validator\Constraints as Assert;

class CreateTaskDto
{
    #[Assert\NotBlank(message: 'Title is required')]
    #[Assert\Length(max: 255)]
    public string $title;

    public ?string $description = null;

    #[Assert\NotBlank(message: 'User ID is required')]
    #[Assert\Type(type: 'integer', message: 'User ID must be an integer')]
    public int $user_id;
}
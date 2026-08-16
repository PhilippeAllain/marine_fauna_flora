<?php

namespace App\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class ContactDTO
{
    #[Assert\NotBlank(message: 'Le nom est obligatoire.')]
    #[Assert\Length(min: 2, max: 50,)]
    public string $name = '';

    #[Assert\NotBlank]
    #[Assert\Email]
    public string $email = '';

    #[Assert\NotBlank(message: 'Le message est obligatoire.')]
    #[Assert\Length(min: 10, max: 200)]
    public string $message = '';

    #[Assert\NotBlank]
    public string $service = '';

}

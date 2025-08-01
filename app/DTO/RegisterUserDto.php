<?php

declare(strict_types=1);

namespace Interns2025b\DTO;

class RegisterUserDto
{
    public function __construct(
        public string $firstName,
        public ?string $lastName,
        public string $email,
        public string $password,
    ) {}
}

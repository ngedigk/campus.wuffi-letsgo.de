<?php

namespace App\Dto;

class AuthenticationResult
{
    public function __construct(
        public bool $success,
        public ?string $error = null
    ) {}
}
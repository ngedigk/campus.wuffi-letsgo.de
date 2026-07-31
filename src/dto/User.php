<?php

final class User
{
    public function __construct(
        public string $id,
        public string $email,
        public bool $isAdmin,
        public bool $emailVerified,
        public ?string $createdAt,
        public ?string $passwordHash = null,
        public ?string $name = null,
    ) {}
}
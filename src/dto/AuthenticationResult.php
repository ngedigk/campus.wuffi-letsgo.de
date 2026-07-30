<?php

class AuthenticationResult
{
    public function __construct(
        public readonly bool $success,
        public readonly ?string $error = null
    ) {}
}
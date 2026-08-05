<?php

namespace App\Contracts;

interface TransactionManager
{
    public function run(callable $callback): mixed;
}
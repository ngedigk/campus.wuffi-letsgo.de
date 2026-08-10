<?php

namespace App\Contracts\Database;

interface TransactionManagerInterface
{
    public function run(callable $callback): mixed;
}
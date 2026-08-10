<?php

namespace App\Container;

use App\Contracts\Database\TransactionManagerInterface;

use App\Infrastructure\Database\PdoTransactionManager;

use \PDO;

trait DatabaseBindings
{
    private function registerDatabase(): void
    {
        $this->set(
            TransactionManagerInterface::class,
            fn ($c) => new PdoTransactionManager($c->get(PDO::class))
        );
    }
}

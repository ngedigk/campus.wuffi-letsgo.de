<?php

namespace App\Container;

use App\Contracts\TransactionManager;

use App\Infrastructure\Database\PdoTransactionManager;

use \PDO;

trait DatabaseBindings
{
    private function registerDatabase(): void
    {
        $this->set(
            TransactionManager::class,
            fn ($c) => new PdoTransactionManager($c->get(PDO::class))
        );
    }
}

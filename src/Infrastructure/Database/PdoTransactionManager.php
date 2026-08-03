<?php

namespace App\Infrastructure\Database;

use App\Contracts\TransactionManager;

use \PDO;
use \Throwable;

class PdoTransactionManager implements TransactionManager
{
    public function __construct(
        private PDO $pdo
    ) {}

    public function run(callable $callback): void
    {
        $this->pdo->beginTransaction();

        try {
            $callback();
            $this->pdo->commit();
        } catch (Throwable $e) {
            $this->pdo->rollBack();
            throw $e;
        }
    }
}
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

    public function run(callable $callback): mixed
    {
        $this->pdo->beginTransaction();

        try {
            $result = $callback();
            
            $this->pdo->commit();

            return $result;
        } catch (Throwable $e) {
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }

            throw $e;
        }
    }
}
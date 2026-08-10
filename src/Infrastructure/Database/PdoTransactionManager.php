<?php

namespace App\Infrastructure\Database;

use App\Contracts\Database\TransactionManagerInterface;

use \PDO;
use \Throwable;

class PdoTransactionManager implements TransactionManagerInterface
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
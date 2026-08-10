<?php

namespace App\Container;

use App\Infrastructure\Database\Database;
use \PDO;

class Container
{
    use DatabaseBindings;
    use MailBindings;
    use RepositoriesBindings;
    use ServicesBindings;
    use HelpersBindings;
    use AdminControllersBindings;
    use ApplicationControllersBindings;
    use MiddlewareBindings;

    private static ?Container $instance = null;
    private array $bindings = [];
    private array $instances = [];

    private function __construct() {}

    public static function getInstance(): static
    {
        if (self::$instance === null) {
            self::$instance = new self();
            self::$instance->registerBindings();
        }

        return self::$instance;
    }

    private function registerBindings(): void
    {
        $pdo = Database::getInstance();

        $this->instances[PDO::class] = $pdo;

        $this->registerDatabase();
        $this->registerMail();
        $this->registerRepositories();
        $this->registerServices();
        $this->registerHelpers();
        $this->registerControllers();
        $this->registerMiddleware();
    }

    private function registerControllers(): void
    {
        $this->registerAdminControllers();
        $this->registerApplicationControllers();
    }

    private function normalizeAbstract(string $abstract): string
    {
        if ($abstract === '' || str_contains($abstract, '\\')) {
            return $abstract;
        }

        $namespaced = 'App\\' . $abstract;

        if (class_exists($namespaced)) {
            return $namespaced;
        }

        return $abstract;
    }

    public function set(string $abstract, callable $concrete): void
    {
        $normalized = $this->normalizeAbstract($abstract);
        $this->bindings[$normalized] = $concrete;
    }

    public function get(string $abstract)
    {
        $normalized = $this->normalizeAbstract($abstract);

        if (isset($this->instances[$normalized])) {
            return $this->instances[$normalized];
        }

        if (!isset($this->bindings[$normalized])) {
            throw new \Exception("Binding not found for {$abstract}");
        }

        $this->instances[$normalized] = $this->bindings[$normalized]($this);

        return $this->instances[$normalized];
    }
}
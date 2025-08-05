<?php

declare(strict_types = 1);

namespace PHPFramework;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DriverManager;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\ORMSetup;

use App\Entity\Users;

/**
 * @mixin Connection
 */
class DB
{
    private Connection $connection;
    private $queryBuilder;
    private $entityManager;

    public function __construct()
    {
        $db = [
            'host'     => DB_SETTINGS['host'],
            'user'     => DB_SETTINGS['username'],
            'password' => DB_SETTINGS['password'],
            'dbname'   => DB_SETTINGS['database'],
            'driver'   => DB_SETTINGS['driver'] ?? 'pdo_pgsql',
        ];

        try {
            $config = ORMSetup::createAttributeMetadataConfiguration(
                paths: [ENTITY],
                isDevMode: true,
            );

            $this->connection = DriverManager::getConnection($db, $config);
            $this->entityManager = new EntityManager($this->connection, $config);
            $this->queryBuilder = $this->entityManager->createQueryBuilder();
        } catch (\PDOException $e) {
            error_log("[" . date('Y-m-d H:i:s') . "] DB Error: {$e->getMessage()}" . PHP_EOL, 3, ERROR_LOGS);
            abort('DB error connection', 500);
        }

        return $this;
    }

    public function queryBuilder() {
        return $this->queryBuilder;
    }

    public function entityManager() {
        return $this->entityManager;
    }

    public function __call(string $name, array $arguments)
    {
        return call_user_func_array([$this->connection, $name], $arguments);
    }
}
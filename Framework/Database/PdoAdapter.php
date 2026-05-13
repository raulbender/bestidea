<?php

declare(strict_types=1);

namespace Framework\Database;

use Exception;
use Framework\Utils\Logger\Logger;
use Framework\Container;
use PDO;
use PDOException;

class PdoAdapter implements DatabaseInterface
{private PDO $pdo;

    public function __construct()
    {
        // Trazemos a lógica do antigo Connection::make() para cá
        $config = Container::$config;
        $host = $config->dbHost ?? $_ENV['DB_HOST'];
        $user = $config->dbUser ?? $_ENV['DB_USER'];
        $pass = $config->dbPass ?? $_ENV['DB_PASS'];
        $dbname = $config->dbName ?? $_ENV['DB_NAME'];

        // Trava de segurança para testes
        if (defined('PHPUNIT_RUNNING') && PHPUNIT_RUNNING === true) {
            if (strpos(strtolower($dbname), 'test') === false) {
                Logger::error("SECURITY BLOCK: Test attempt at the bank '$dbname'");
                throw new Exception("DANGER! PHPUnit attempted to access the database '$dbname'. Use a database that contains 'test' in the name!");
            }
        }

        $dsn = "mysql:host=$host;dbname=$dbname;charset=utf8mb4";

        try {
            $this->pdo = new PDO($dsn, $user, $pass, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4",
            ]);
            $this->pdo->exec("SET time_zone = '+00:00'");
        } catch (PDOException $e) {
            Logger::error("Connection failed: " . $e->getMessage());
            throw $e;
        }
    }


    public function beginTransaction(): bool
    {
        return $this->pdo->beginTransaction();
    }

    public function commit(): bool
    {
        return $this->pdo->commit();
    }
    
    public function rollBack(): bool
    {
        return $this->pdo->rollBack();
    }



    public function query(string $sql, object|array $params = []): \PDOStatement
    {
        $data = is_object($params) ? get_object_vars($params) : $params;
        Logger::sql($sql, $data);
        $stmt = $this->pdo->prepare($sql);

        foreach ($data as $key => $value) {
            $parameter = is_int($key) ? $key + 1 : $key;
            $stmt->bindValue($parameter, $value);
        }

        $stmt->execute();

        return $stmt;
    }



    public function lastInsertId(): string
    {
        $id = $this->pdo->lastInsertId();
        if ($id === false) {
            throw new \Exception("FATAL ERROR: Could not retrieve the last entered ID.");
        }

        return $id;
    }


    /**
     * @param array<string|int, mixed> $params
     * @return array<string, mixed>|null */
    public function fetchAssoc(string $sql, array $params = []): ?array
    {
        $stmt = $this->query($sql, $params);
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);
        if ($result === false) {
            return null;
        }

        /** @var array<string, mixed> $result */
        return $result;
    }





    //--------------------------New methods--------------------------

    /** @return array<string, mixed> */
    public function entityToArrayNotNull(object $entity): array
    {
        return array_filter(get_object_vars($entity), fn ($v) => ! is_null($v));
    }


    public function queryAsClass(string $sql, ?object $entity = null): \PDOStatement
    {
        $className = $entity ? get_class($entity) : \stdClass::class;
        $params = $entity ? $this->entityToArrayNotNull($entity) : [];

        Logger::sql($sql, $params);
        $stmt = $this->pdo->prepare($sql);

        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }

        $stmt->setFetchMode(\PDO::FETCH_CLASS | \PDO::FETCH_PROPS_LATE, $className);

        $stmt->execute();

        return $stmt;
    }


    public function insert(string $table, object $entity): bool
    {
        $params = $this->entityToArrayNotNull($entity);

        $columns = implode(', ', array_keys($params));
        $placeholders = ':' . implode(', :', array_keys($params));

        $sql = "INSERT INTO {$table} ({$columns}) VALUES ({$placeholders})";

        $stmt = $this->pdo->prepare($sql);

        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }

        $stmt->execute();
        Logger::sql($sql, $params);

        return $stmt->rowCount() > 0;
    }

    /** @param array<string, mixed>|object $params */
    public function queryNewMethod(string $sql, array|object $params = []): \PDOStatement
    {
        $data = is_object($params) ? get_object_vars($params) : $params;
        Logger::sql($sql, $data);

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($data);

        return $stmt;
    }


    public function selectOneByParam(string $table, object $entity): ?object
    {
        $className = get_class($entity);

        $data = $this->entityToArrayNotNull($entity);

        if (empty($data)) {
            throw new \InvalidArgumentException("The object provided does not have any populated search criteria.");
        }

        $whereParts = [];
        foreach (array_keys($data) as $column) {
            $whereParts[] = "{$column} = :{$column}";
        }

        $sql = sprintf(
            "SELECT * FROM %s WHERE %s LIMIT 1",
            $table,
            implode(' AND ', $whereParts)
        );

        Logger::sql($sql, $data);

        $stmt = $this->pdo->prepare($sql);

        $stmt->execute($data);

        $stmt->setFetchMode(\PDO::FETCH_CLASS | \PDO::FETCH_PROPS_LATE, $className);

        /** @var object|false $result */
        $result = $stmt->fetch();

        return $result ?: null;
    }

    /** @return array<int, object> */
    public function selectAllByParam(string $table, object $entity): array
    {
        $className = get_class($entity);
        $data = $this->entityToArrayNotNull($entity);

        $whereClause = "";
        if (! empty($data)) {
            $whereParts = [];
            foreach (array_keys($data) as $column) {
                $whereParts[] = "{$column} = :{$column}";
            }
            $whereClause = " WHERE " . implode(' AND ', $whereParts);
        }

        $sql = sprintf(
            "SELECT * FROM %s %s",
            $table,
            $whereClause
        );

        Logger::sql($sql, $data);

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($data);

        $stmt->setFetchMode(\PDO::FETCH_CLASS | \PDO::FETCH_PROPS_LATE, $className);

        return $stmt->fetchAll();
    }


    public function selectOneById(string $table, int|string $id, string $className = \stdClass::class): ?object
    {
        $sql = "SELECT * FROM {$table} WHERE id = :id LIMIT 1";

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':id', $id);
        $stmt->execute();

        $stmt->setFetchMode(\PDO::FETCH_CLASS, $className);

        /** @var object|false $result */
        $result = $stmt->fetch();

        return $result ?: null;
    }


    public function update(string $table, object $what, object $where): bool
    {
        $whatData = $this->entityToArrayNotNull($what);
        $whereData = $this->entityToArrayNotNull($where);

        $setParts = [];
        foreach (array_keys($whatData) as $column) {
            $setParts[] = "{$column} = :{$column}";
        }

        $whereParts = [];
        $whereParams = [];
        foreach ($whereData as $column => $value) {
            $whereParts[] = "{$column} = :w_{$column}";
            $whereParams["w_{$column}"] = $value;
        }

        $sql = sprintf(
            "UPDATE %s SET %s WHERE %s",
            $table,
            implode(', ', $setParts),
            implode(' AND ', $whereParts)
        );

        $stmt = $this->pdo->prepare($sql);

        foreach ($whatData as $key => $value) {
            $stmt->bindValue(":{$key}", $value);
        }

        foreach ($whereParams as $key => $value) {
            $stmt->bindValue(":{$key}", $value);
        }

        $stmt->execute();

        return $stmt->rowCount() > 0;
    }

    /**
     * Remove registros baseados nos critérios de um objeto.
     */
    public function delete(string $table, object $where): bool
    {
        $whereData = $this->entityToArrayNotNull($where);

        if (empty($whereData)) {
            throw new Exception("CUIDADO: Tentativa de delete sem critérios (WHERE) detectada.");
        }

        $whereParts = [];
        foreach (array_keys($whereData) as $column) {
            $whereParts[] = "{$column} = :{$column}";
        }

        $sql = sprintf(
            "DELETE FROM %s WHERE %s",
            $table,
            implode(' AND ', $whereParts)
        );

        $stmt = $this->pdo->prepare($sql);
        foreach ($whereData as $key => $value) {
            $stmt->bindValue(":{$key}", $value);
        }

        return $stmt->execute();
    }


    public function table(string $table): QueryBuilder
    {
        return new QueryBuilder($this, $table);
    }

}

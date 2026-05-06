<?php

declare(strict_types=1);

namespace Framework\Database;

interface DatabaseInterface
{
    /** @param array<int|string, mixed> $params */
    public function query(string $sql, array $params = []): \PDOStatement;

    public function lastInsertId(): string;

    /**
     * @param array<string|int, mixed> $params
     * @return array<string, mixed>|null  */
    public function fetchAssoc(string $sql, array $params = []): ?array;


    public function beginTransaction(): bool;
    public function commit(): bool;
    public function rollBack(): bool;

    //--------New method for updates and deletes that return affected rows count --------
    /** @return array<string, mixed>
     * Converte as propriedades públicas de um objeto em um array associativo, ignorando as propriedades com valor null.
     */
    public function entityToArrayNotNull(object $entity): array;
    /**
     * Insere um registro baseado nas propriedades públicas do objeto.
     * @param string $table Nome da tabela.
     * @param object $entity Objeto cujas propriedades coincidem com as colunas do banco.
     */
    public function insert(string $table, object $entity): bool;
    /**
     * Atualiza registros baseados em objetos de dados e critérios.
     * @param object $what Objeto com as novas propriedades.
     * @param object $where Objeto com as propriedades para o filtro WHERE.
     */
    public function update(string $table, object $what, object $where): bool;
    /**
     * Remove registros baseados em propriedades de um objeto.
     * @param object $where Critérios de exclusão.
     */
    public function delete(string $table, object $where): bool;
    public function selectOneById(string $table, int|string $id, string $className = \stdClass::class): ?object;
    public function selectOneByParam(string $table, object $entity): ?object;
    /** @return array<int, object> */
    public function selectAllByParam(string $table, object $entity): array;
    public function queryAsClass(string $sql, ?object $entity = null): \PDOStatement;
    public function table(string $table): QueryBuilder;
}

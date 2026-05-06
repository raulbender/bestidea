<?php

declare(strict_types=1);

namespace App\Database\Migrations;

use Framework\Database\DatabaseInterface;
use Framework\Database\MigrationInterface;

class m2026_05_06_191407_create_authors_table implements MigrationInterface
{
    public function up(DatabaseInterface $db): void
    {
        $sql = "CREATE TABLE authors (
                id INT AUTO_INCREMENT PRIMARY KEY,
                name VARCHAR(100) NOT NULL,
                avatar TEXT, -- Pode ser uma letra ou caminho de imagem
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";

        $db->query($sql);
    }

    public function down(DatabaseInterface $db): void
    {
        $db->query("DROP TABLE IF EXISTS authors");
    }
}
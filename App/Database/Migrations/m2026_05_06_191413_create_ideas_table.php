<?php

declare(strict_types=1);

namespace App\Database\Migrations;

use Framework\Database\DatabaseInterface;
use Framework\Database\MigrationInterface;

class m2026_05_06_191413_create_ideas_table implements MigrationInterface
{
    public function up(DatabaseInterface $db): void
    {
        $sql = "CREATE TABLE ideas (
                id INT AUTO_INCREMENT PRIMARY KEY,
                author_id INT NOT NULL,
                content TEXT NOT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (author_id) REFERENCES authors(id) ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";

        $db->query($sql);
    }

    public function down(DatabaseInterface $db): void
    {
        $db->query("DROP TABLE IF EXISTS ideas");
    }
}
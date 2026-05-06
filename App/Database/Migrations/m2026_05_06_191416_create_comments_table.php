<?php

declare(strict_types=1);

namespace App\Database\Migrations;

use Framework\Database\DatabaseInterface;
use Framework\Database\MigrationInterface;

class m2026_05_06_191416_create_comments_table implements MigrationInterface
{
    public function up(DatabaseInterface $db): void
    {
        $sql = "CREATE TABLE comments (
                id INT AUTO_INCREMENT PRIMARY KEY,
                idea_id INT NOT NULL,
                author_id INT NOT NULL,
                content TEXT NOT NULL,
                rating TINYINT DEFAULT 0, -- Aqui moram as 1 a 5 estrelas
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (idea_id) REFERENCES ideas(id) ON DELETE CASCADE,
                FOREIGN KEY (author_id) REFERENCES authors(id) ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";

        $db->query($sql);
    }

    public function down(DatabaseInterface $db): void
    {
        $db->query("DROP TABLE IF EXISTS comments");
    }
}
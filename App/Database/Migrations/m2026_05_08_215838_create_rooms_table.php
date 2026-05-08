<?php

declare(strict_types=1);

namespace App\Database\Migrations;

use Framework\Database\DatabaseInterface;
use Framework\Database\MigrationInterface;

class m2026_05_08_215838_create_rooms_table implements MigrationInterface {

    public function up(DatabaseInterface $db): void {
        $sql = "CREATE TABLE IF NOT EXISTS rooms (
        id INT AUTO_INCREMENT PRIMARY KEY,
        uuid VARCHAR(36) NOT NULL UNIQUE,
        description TEXT NOT NULL,
        expires_at DATETIME NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        INDEX idx_room_uuid (uuid)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";

        $db->query($sql);
    }

    public function down(DatabaseInterface $db): void {
        $db->query("DROP TABLE IF EXISTS rooms");
    }
}

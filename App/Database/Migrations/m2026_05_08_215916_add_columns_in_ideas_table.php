<?php

declare(strict_types=1);

namespace App\Database\Migrations;

use Framework\Database\DatabaseInterface;
use Framework\Database\MigrationInterface;

class m2026_05_08_215916_add_columns_in_ideas_table implements MigrationInterface {

    public function up(DatabaseInterface $db): void {
        $sql = "ALTER TABLE ideas 
            ADD COLUMN room_id INT NULL AFTER id,
            ADD CONSTRAINT fk_ideas_room FOREIGN KEY (room_id) REFERENCES rooms(id) ON DELETE CASCADE,
            ADD INDEX idx_ideas_room (room_id);";

        $db->query($sql);
    }

    public function down(DatabaseInterface $db): void {
        $db->query("ALTER TABLE ideas DROP FOREIGN KEY fk_ideas_room");
        $db->query("ALTER TABLE ideas DROP COLUMN room_id");
    }
}

<?php

declare(strict_types=1);

namespace App\Database\Migrations;

use Framework\Database\DatabaseInterface;
use Framework\Database\MigrationInterface;

class m2026_05_07_115014_add_type_to_authors_table implements MigrationInterface
{
    public function up(DatabaseInterface $db): void
    {
        $sql = "ALTER TABLE authors 
                ADD COLUMN type TINYINT DEFAULT 0 AFTER avatar;";

        $db->query($sql);
    }

    public function down(DatabaseInterface $db): void
    {
        $db->query("ALTER TABLE authors DROP COLUMN type");
    }
}
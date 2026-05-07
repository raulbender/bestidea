<?php

declare(strict_types=1);

namespace App\Database\Migrations;

use Framework\Database\DatabaseInterface;
use Framework\Database\MigrationInterface;

class m2026_05_07_122902_seed_initial_authors implements MigrationInterface {
    private function getSeedData(): array {
        return [
            // IA - Frutas (Type 1)
            ['author.fruit.avocado', '🥑', 1],
            ['author.fruit.lemon',   '🍋', 1],
            ['author.fruit.apple',   '🍎', 1],
            ['author.fruit.grape',   '🍇', 1],

            // Humanos - Animais (Type 0)
            ['author.animal.lion',  '🦁', 0],
            ['author.animal.fox',   '🦊', 0],
            ['author.animal.panda', '🐼', 0],
            ['author.animal.owl',   '🦉', 0],
            ['author.animal.shark', '🦈', 0],
            ['author.animal.tiger', '🐯', 0],
            ['author.animal.bear',  '🐻', 0],
            ['author.animal.koala', '🐨', 0],
            ['author.animal.rabbit', '🐰', 0],
            ['author.animal.wolf',  '🐺', 0],
            ['author.animal.frog',  '🐸', 0],
            ['author.animal.monkey', '🐵', 0],
            ['author.animal.pig',   '🐷', 0],
            ['author.animal.dog',   '🐶', 0],
            ['author.animal.cat',   '🐱', 0],
            ['author.animal.mouse', '🐭', 0],
            ['author.animal.hamster', '🐹', 0],
            ['author.animal.dragon', '🐲', 0],
            ['author.animal.whale', '🐳', 0],
            ['author.animal.octopus', '🐙', 0],
            ['author.animal.crab',  '🦀', 0],
            ['author.animal.bee',   '🐝', 0],
            ['author.animal.butterfly', '🦋', 0],
            ['author.animal.turtle', '🐢', 0],
            ['author.animal.snake', '🐍', 0],
            ['author.animal.horse', '🐴', 0],
            ['author.animal.sheep', '🐑', 0],
            ['author.animal.elephant', '🐘', 0],
            ['author.animal.giraffe', '🦒', 0],
            ['author.animal.penguin', '🐧', 0],
            ['author.animal.duck',  '🦆', 0],
            ['author.animal.bat',   '🦇', 0],
            ['author.animal.eagle', '🦅', 0],
            ['author.animal.boar',  '🐗', 0]
        ];
    }

    public function up(DatabaseInterface $db): void {
        foreach ($this->getSeedData() as $item) {
            $db->query(
                "INSERT INTO authors (name, avatar, type) VALUES (?, ?, ?)",
                [$item[0], $item[1], $item[2]]
            );
        }
    }

    public function down(DatabaseInterface $db): void {
        foreach ($this->getSeedData() as $item) {
            $db->query(
                "DELETE FROM authors WHERE name = ?",
                [$item[0]]
            );
        }
    }
}

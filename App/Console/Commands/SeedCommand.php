<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Framework\Console\CommandInterface;
use Framework\Database\DatabaseInterface;

class SeedCommand implements CommandInterface
{
    public function __construct(private DatabaseInterface $db) {}

    public function getDescription(): string
    {
        return 'Semeia o banco de dados com ideias e comentários fictícios para testes.';
    }

    public function execute(array $args): void
    {
        echo "🌱 Semeando dados fictícios no Volt R²...\n";

        // 1. Pegar autores para distribuir as ideias
        $fruits = $this->db->query("SELECT id FROM authors WHERE type = 1")->fetchAll(\PDO::FETCH_ASSOC);
        $animals = $this->db->query("SELECT id FROM authors WHERE type = 0")->fetchAll(\PDO::FETCH_ASSOC);

        if (empty($fruits) || empty($animals)) {
            echo "❌ Erro: Execute as migrations de autores primeiro!\n";
            return;
        }

        $ideiasFicticias = [
            "Como escalar o Volt R² usando micro-serviços em Rust?",
            "Sugestão: Adicionar um sistema de Webhooks no BestIdea.",
            "E se o frontend usasse WebComponents puros em vez de React?",
            "Precisamos de uma integração com a API da OpenAI para as frutas comentarem sozinhas!"
        ];

        foreach ($ideiasFicticias as $content) {
            // Sorteia um animal (humano) para ser o autor da ideia
            $authorId = $animals[array_rand($animals)]['id'];
            
            $this->db->query("INSERT INTO ideas (author_id, content) VALUES (?, ?)", [$authorId, $content]);
            $ideaId = $this->db->lastInsertId();

            // Criar 2 comentários de frutas (IA) para cada ideia
            for ($i = 0; $i < 2; $i++) {
                $fruitId = $fruits[array_rand($fruits)]['id'];
                $this->db->query(
                    "INSERT INTO comments (idea_id, author_id, content, rating) VALUES (?, ?, ?, ?)",
                    [$ideaId, $fruitId, "Comentário automático da IA sobre: " . substr($content, 0, 20) . "...", rand(1, 5)]
                );
            }
        }

        echo "✅ Sucesso! O mar está cheio de ideias.\n";
    }
}
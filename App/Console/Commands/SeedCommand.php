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
        return 'Semeia o banco de dados com salas, ideias e comentários fictícios.';
    }

    public function execute(array $args): void
    {
        echo "🌱 Semeando dados fictícios no Volt R²...\n";

        // 1. Verificar/Buscar a sala específica que você mencionou
        $targetUuid = "1039b2d84b5344162a37417b15ee8c82";
        $room = $this->db->query("SELECT id FROM rooms WHERE uuid = ?", [$targetUuid])->fetch(\PDO::FETCH_ASSOC);

        if (!$room) {
            echo "⚠️ Sala específica não encontrada. Criando sala padrão para o Seed...\n";
            $expiresAt = (new \DateTime())->modify('+24 hours')->format('Y-m-d H:i:s');
            $this->db->query(
                "INSERT INTO rooms (uuid, description, expires_at) VALUES (?, ?, ?)",
                [$targetUuid, 'Sala de Brainstorming Internacional', $expiresAt]
            );
            $roomId = $this->db->lastInsertId();
        } else {
            $roomId = $room['id'];
        }

        // 2. Pegar autores (Frutas e Animais)
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
            
            // AGORA INCLUÍMOS O room_id NA INSERÇÃO
            $this->db->query(
                "INSERT INTO ideas (room_id, author_id, content) VALUES (?, ?, ?)", 
                [$roomId, $authorId, $content]
            );
            
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

        echo "✅ Seed finalizado com sucesso para a sala: $targetUuid\n";
    }
}
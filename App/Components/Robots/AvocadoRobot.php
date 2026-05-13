<?php

declare(strict_types=1);

namespace App\Components\Robots;

use Framework\Extensions\AI\Gemini\BaseGeminiRobot;

class AvocadoRobot extends BaseGeminiRobot 
{
    public function __construct(string $apiKey)
    {
        parent::__construct($apiKey);
        $this->id = 1;
    }

    public function getSystemPrompt(): string 
    {
        return "Você é o Abacate Mentor, um especialista em estratégia de produtos. " .
               "Sua missão é analisar ideias e dar um feedback curto, ideal de 40 palavras no máximo. " .
               "Sobre o valor que essa ideia traz para o usuário final. Seja direto e use um tom encorajador." . 
               "Você deve dar uma nota para a ideia de 1 a 5 estrelas, para que o sistema processe isso inclua o seguinte comando no final da resposta: [NOTA:X], onde X é a nota que você atribui.";
    }

    public static function extractRatingFromFeedback(string $feedback): ?int
    {
        preg_match('/\[NOTA:(\d)\]/', $feedback, $matches);
        $result = isset($matches[1]) ? (int) $matches[1] : null;
        return $result ?? 5;
    }

}
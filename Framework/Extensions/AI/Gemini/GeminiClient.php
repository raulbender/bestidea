<?php

declare(strict_types=1);

namespace Framework\Extensions\AI\Gemini;

use Framework\Extensions\AI\AiClientInterface;
use Framework\Utils\Logger\Logger;

class GeminiClient implements AiClientInterface
{
    /** @param array<int, mixed> $history */
    public function generateResponseFromRobot(BaseGeminiRobot $robot, array $history): ?string
    {
        $url = $robot->getApiUrl();
        $payload = $robot->buildPayload($history);

        return $this->post($url, $payload);
    }

    // Método genérico exigido pela Interface
    public function generateResponse(array $payload): ?string
    {
        // Aqui você implementaria uma chamada genérica caso não tenha o objeto Robot
        return null;
    }

    /**  @param array<string, mixed> $payload */
    private function post(string $url, array $payload): ?string
    {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode !== 200) {
            Logger::error("Gemini API Error: HTTP $httpCode - Response: $response");

            return null;
        }

        $data = json_decode((string)$response, true);
        if (! is_array($data)) {
            return null;
        }
        $result = $data['candidates'][0]['content']['parts'][0]['text'];

        return ensureStringOrNull($result);
    }
}

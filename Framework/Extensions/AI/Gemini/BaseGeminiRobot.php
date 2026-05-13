<?php

declare(strict_types=1);

namespace Framework\Extensions\AI\Gemini;

abstract class BaseGeminiRobot
{
    public ?int $id = null;
    public ?string $name = null;

    public const FLASH_LITE = "gemini-flash-lite-latest";
    public const FLASH = "gemini-flash-latest";
    public const PRO = "gemini-pro-latest";

    public const SAFETY_NONE = 'BLOCK_NONE';
    public const SAFETY_LOW = 'BLOCK_ONLY_HIGH'; // Bloqueia pouco
    public const SAFETY_MED = 'BLOCK_MEDIUM_AND_ABOVE';
    public const SAFETY_HIGH = 'BLOCK_LOW_AND_ABOVE'; // Bloqueia muito

    private const BASE_URL = "https://generativelanguage.googleapis.com/v1beta/models/";

    protected string $model = self::FLASH_LITE;

    protected float $temperature = 0.7;
    protected float $topP = 1.0;
    protected int $maxOutputTokens = 2048;

    protected string $harassmentThreshold = self::SAFETY_NONE;
    protected string $hateSpeechThreshold = self::SAFETY_NONE;
    protected string $sexuallyExplicitThreshold = self::SAFETY_NONE;
    protected string $dangerousContentThreshold = self::SAFETY_NONE;

    public function __construct(
        protected string $apiKey
    ) {
    }

    abstract public function getSystemPrompt(): string;

    public function getApiUrl(): string
    {
        return self::BASE_URL . $this->model . ":generateContent?key=" . $this->apiKey;
    }

    public function setModel(string $model): self
    {
        $this->model = $model;

        return $this;
    }


    public function setTemperature(float $temp): self
    {
        $this->temperature = $temp;

        return $this;
    }

    public function setHarassmentThreshold(string $threshold): self
    {
        $this->harassmentThreshold = $threshold;

        return $this;
    }

    public function setHateSpeechThreshold(string $threshold): self
    {
        $this->hateSpeechThreshold = $threshold;

        return $this;
    }

    public function setSexuallyExplicitThreshold(string $threshold): self
    {
        $this->sexuallyExplicitThreshold = $threshold;

        return $this;
    }

    public function setDangerousContentThreshold(string $threshold): self
    {
        $this->dangerousContentThreshold = $threshold;

        return $this;
    }

    /**
     * @param array<int, mixed> $history
     * @return array<string, mixed>
     */
    public function buildPayload(array $history): array
    {
        return [
            "system_instruction" => [
                "parts" => [["text" => $this->getSystemPrompt()]],
            ],
            "contents" => $history,
            "safetySettings" => [
                ["category" => "HARM_CATEGORY_HARASSMENT", "threshold" => $this->harassmentThreshold],
                ["category" => "HARM_CATEGORY_HATE_SPEECH", "threshold" => $this->hateSpeechThreshold],
                ["category" => "HARM_CATEGORY_SEXUALLY_EXPLICIT", "threshold" => $this->sexuallyExplicitThreshold],
                ["category" => "HARM_CATEGORY_DANGEROUS_CONTENT", "threshold" => $this->dangerousContentThreshold],
            ],
            "generationConfig" => [
                "temperature" => $this->temperature,
                "topP" => $this->topP,
                "maxOutputTokens" => $this->maxOutputTokens,
            ],
        ];
    }
}

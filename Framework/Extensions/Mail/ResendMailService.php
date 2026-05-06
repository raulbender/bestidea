<?php

declare(strict_types=1);

namespace Framework\Extensions\Mail;

use Framework\Container;
use Framework\Utils\Logger\Logger;

class ResendMailService implements MailInterface
{
    private string $apiKey;
    private string $defaultFrom;

    public function __construct()
    {
        $this->apiKey = Container::$config->resendApiKey ?? $_ENV['RESEND_API_KEY'];
        $this->defaultFrom = Container::$config->mailFromDefault ?? 'onboarding@resend.dev';
    }

    public function send(MailMessageDTO $message): bool
    {
        $payload = [
            'from' => $message->from ?? $this->defaultFrom,
            'to' => [$message->to],
            'subject' => $message->subject,
            'html' => $message->body,
        ];

        $ch = curl_init('https://api.resend.com/emails');

        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => json_encode($payload),
            CURLOPT_HTTPHEADER => [
                'Authorization: Bearer ' . $this->apiKey,
                'Content-Type: application/json',
            ],
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode >= 200 && $httpCode < 300) {
            return true;
        }

        Logger::error("Failed to send email via Resend. HTTP Code: $httpCode, Response: $response");

        return false;
    }
}

<?php

declare(strict_types=1);

namespace Framework\Console\Commands;

use Framework\Console\CommandInterface;
use Framework\Extensions\Mail\MailInterface;
use Framework\Extensions\Mail\MailMessageDTO;

class TestMail implements CommandInterface
{
    public function __construct(private MailInterface $mailService)
    {
    }

    public function getDescription(): string
    {
        return "Trigger a test email via Resend to validate the integration.";
    }

    /** @param array<int, string> $args */
    public function execute(array $args): void
    {
        $to = $args[0] ?? null;

        if (! $to) {
            echo "❌ No destination! Use: php volt test:mail your-email@example.com\n";

            return;
        }

        echo "📧 Preparing shipping for {$to}...\n";

        $message = new MailMessageDTO(
            to: $to,
            subject: "Onboard Message!",
            body: "<h1>Greetings!</h1><p>You has just conquered the skies over Resend via cURL.</p>"
        );

        if ($this->mailService->send($message)) {
            echo "✅ Email sent successfully! Check your inbox (and spam just in case).\n";
        } else {
            echo "❌ Failed to send email. Check your API Key and if the recipient is authorized in Resend's Sandbox.\n";
        }
    }
}

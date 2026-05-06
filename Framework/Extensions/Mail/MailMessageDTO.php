<?php

declare(strict_types=1);

namespace Framework\Extensions\Mail;

readonly class MailMessageDTO
{
    public function __construct(
        public string $to,
        public string $subject,
        public string $body,
        public ?string $from = null
    ) {
    }
}

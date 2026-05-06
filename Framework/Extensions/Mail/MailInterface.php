<?php

declare(strict_types=1);

namespace Framework\Extensions\Mail;

interface MailInterface
{
    public function send(MailMessageDTO $message): bool;
}

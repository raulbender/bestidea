<?php

declare(strict_types=1);

namespace Framework\Extensions\Payment;

interface WebhookHandlerInterface
{
    public function handlePaymentEvent(\stdClass $event): void;
}

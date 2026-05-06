<?php

namespace Framework\Extensions\Payment;

class StripeDTO
{
    public ?string $productName = null;
    public ?string $description = null;
    public ?int $priceInCents = null;
    public int $quantity = 1;
    public string $currency = 'brl';

    /** @var array<string, string> */
    public array $metadata = [];
    public ?string $successUrl = null;
    public ?string $cancelUrl = null;

    public function isValid(): bool
    {
        return ! empty($this->productName) && ! empty($this->priceInCents) && $this->priceInCents > 0;
    }

}

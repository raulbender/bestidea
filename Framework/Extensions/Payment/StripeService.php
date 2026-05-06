<?php

namespace Framework\Extensions\Payment;

use Framework\Container;
use Framework\Utils\Navigation;
use Stripe\Checkout\Session;
use Stripe\Stripe;

class StripeService
{
    public function driveToPayment(StripeDTO $entity): void
    {
        if (! $entity->isValid()) {
            throw new \InvalidArgumentException("Incomplete payment details for launch.");
        }

        Stripe::setApiKey(Container::$config->stripeSecretKey);

        $session = Session::create([
            'payment_method_types' => ['card'],
            'line_items' => [[
                'price_data' => [
                    'currency' => $entity->currency,
                    'product_data' => [
                        'name' => ensureString($entity->productName),
                        'description' => ensureString($entity->description),
                    ],
                    'unit_amount' => ensureInt($entity->priceInCents, ),
                ],
                'quantity' => $entity->quantity,
            ]],
            'mode' => 'payment',
            'success_url' => ensureString($entity->successUrl),
            'cancel_url' => ensureString($entity->cancelUrl),
            'metadata' => $entity->metadata,
        ]);

        header("HTTP/1.1 303 See Other");
        Navigation::redirect($session->url ?? '/');
    }
}

<?php

declare(strict_types=1);

namespace Framework\Extensions\Payment;

use Framework\BaseController;
use Framework\Container;
use Framework\Http\Request;
use Stripe\Exception\SignatureVerificationException;
use Stripe\Webhook;

class WebhookController extends BaseController
{
    public function __construct(
        private WebhookHandlerInterface $webhookHandler,
    ) {
        parent::__construct();
    }


    public function handleStripe(Request $request): void
    {
        $endpoint_secret = Container::$config->webhookSecret;

        $payload = ensureString(@file_get_contents('php://input'));

        $sig_header = ensureString($request->getHeader('Stripe-Signature'));

        try {
            $event = Webhook::constructEvent($payload, $sig_header, $endpoint_secret);
            if ($event->type === 'checkout.session.completed') {
                $this->webhookHandler->handlePaymentEvent((object)$event->jsonSerialize());
                //$this->webhookHandler->handlePaymentEvent($event);
            }
            http_response_code(200);
        } catch (SignatureVerificationException $e) {            
          throw new \Exception("Webhook Signature Failure: " . $e->getMessage(), 500);
        }
    }
}

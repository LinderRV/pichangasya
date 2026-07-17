<?php

namespace App\Http\Controllers;

use App\Services\ReservaPagoService;
use App\Services\StripeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Stripe\Exception\SignatureVerificationException;
use UnexpectedValueException;

class StripeWebhookController extends Controller
{
    public function __construct(
        private StripeService $stripe,
        private ReservaPagoService $reservaPago
    ) {}

    public function handle(Request $request)
    {
        try {
            $event = $this->stripe->verificarWebhook(
                $request->getContent(),
                (string) $request->header('Stripe-Signature')
            );
        } catch (SignatureVerificationException|UnexpectedValueException $e) {
            Log::warning('Webhook Stripe rechazado: ' . $e->getMessage());
            return response()->json(['error' => 'invalid'], 400);
        }

        if ($event->type === 'checkout.session.completed') {
            $session = $event->data->object;

            if ($session->payment_status === 'paid') {
                $this->reservaPago->confirmarPagoStripe(
                    $session->metadata->toArray(),
                    (string) $session->payment_intent
                );
            }
        }

        return response()->json(['received' => true]);
    }
}

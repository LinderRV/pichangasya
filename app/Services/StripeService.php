<?php

namespace App\Services;

use Stripe\Checkout\Session as CheckoutSession;
use Stripe\Event;
use Stripe\StripeClient;
use Stripe\Webhook;

class StripeService
{
    private StripeClient $client;

    public function __construct()
    {
        $this->client = new StripeClient(config('stripe.secret'));
    }

    public function crearCheckoutSession(array $pending, string $successUrl, string $cancelUrl, string $email): CheckoutSession
    {
        return $this->client->checkout->sessions->create([
            'mode'                => 'payment',
            'customer_email'      => $email,
            'client_reference_id' => $pending['purchase_number'],
            'line_items'          => [[
                'price_data' => [
                    'currency'     => config('stripe.currency'),
                    'product_data' => [
                        'name' => 'Reserva de cancha - ' . $pending['purchase_number'],
                    ],
                    'unit_amount' => (int) round($pending['total'] * 100),
                ],
                'quantity' => 1,
            ]],
            'success_url' => $successUrl . '?session_id={CHECKOUT_SESSION_ID}',
            'cancel_url'  => $cancelUrl,
            'metadata'    => [
                'id_cancha'       => $pending['id_cancha'],
                'id_cliente'      => $pending['id_cliente'],
                'id_usuario'      => $pending['id_usuario'],
                'fecha'           => $pending['fecha'],
                'hora_inicio'     => $pending['hora_inicio'],
                'hora_fin'        => $pending['hora_fin'],
                'precio_hora'     => $pending['precio_hora'],
                'total'           => $pending['total'],
                'purchase_number' => $pending['purchase_number'],
            ],
        ]);
    }

    public function obtenerSesion(string $sessionId): CheckoutSession
    {
        return $this->client->checkout->sessions->retrieve($sessionId);
    }

    public function verificarWebhook(string $payload, string $sigHeader): Event
    {
        return Webhook::constructEvent($payload, $sigHeader, config('stripe.webhook_secret'));
    }

    public static function generarPurchaseNumber(): string
    {
        return (string) (time() . random_int(100, 999));
    }
}

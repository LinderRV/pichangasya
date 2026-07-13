<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Exception;

class NiubizService
{
    private string $merchantId;
    private string $apiUrl;
    private string $username;
    private string $password;

    public function __construct()
    {
        $this->merchantId = config('niubiz.merchant_id');
        $this->apiUrl     = config('niubiz.api_url');
        $this->username   = config('niubiz.username');
        $this->password   = config('niubiz.password');
    }

    private function accessToken(): string
    {
        $response = Http::withBasicAuth($this->username, $this->password)
            ->withoutVerifying()   // entorno de pruebas Niubiz
            ->get("{$this->apiUrl}/api.security/v1/security");

        if (!$response->successful()) {
            throw new Exception('Error al obtener token Niubiz (' . $response->status() . '): ' . $response->body());
        }

        return trim($response->body());
    }

    public function crearSesion(float $amount, string $clientIp, string $email): string
    {
        $token = $this->accessToken();

        // Niubiz NO "Bearer",  token directo en Authorization
        $response = Http::withHeaders(['Authorization' => $token])
            ->withoutVerifying()
            ->post("{$this->apiUrl}/api.ecommerce/v2/ecommerce/token/session/{$this->merchantId}", [
                'channel' => 'web',
                'amount'  => $amount,
                'antifraud' => [
                    'clientIp' => $clientIp,
                    'merchantDefineData' => [
                        'MDD4'  => $email,
                        'MDD21' => 0,
                        'MDD32' => 'cliente',
                        'MDD75' => 'Registrado',
                        'MDD77' => 0,
                    ],
                ],
            ]);

        if (!$response->successful()) {
            throw new Exception('Error al crear sesión Niubiz (' . $response->status() . '): ' . $response->body());
        }

        $sessionKey = $response->json('sessionKey');

        if (!$sessionKey) {
            throw new Exception('Respuesta de Niubiz sin sessionKey: ' . $response->body());
        }

        return $sessionKey;
    }

    public function autorizar(string $transactionToken, string $purchaseNumber, float $amount, string $clientIp, string $email): array
    {
        $token = $this->accessToken();

        $response = Http::withHeaders(['Authorization' => $token])
            ->withoutVerifying()
            ->post("{$this->apiUrl}/api.authorization/v3/authorization/ecommerce/{$this->merchantId}", [
                'antifraud' => [
                    'clientIp' => $clientIp,
                    'merchantDefineData' => [
                        'MDD4'  => $email,
                        'MDD21' => 0,
                        'MDD32' => 'cliente',
                        'MDD75' => 'Registrado',
                        'MDD77' => 0,
                    ],
                ],
                'captureType' => 'automatic',
                'channel'     => 'web',
                'countable'   => true,
                'order'       => [
                    'amount'         => $amount,
                    'currency'       => 'PEN',
                    'purchaseNumber' => $purchaseNumber,
                    'tokenId'        => $transactionToken,
                ],
            ]);

        return $response->json() ?? [];
    }

    public static function generarPurchaseNumber(): string
    {
        return (string) (time() . random_int(100, 999));
    }
}

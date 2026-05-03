<?php
namespace Utils;

class Paypal
{
    private string $clientId;
    private string $secret;
    private string $baseUrl = 'https://api-m.sandbox.paypal.com';

    public function __construct()
    {
        $this->clientId = $_ENV['PAYPAL_CLIENT_ID'];
        $this->secret   = $_ENV['PAYPAL_SECRET'];
    }

    private function getAccessToken(): string
    {
        $ch = curl_init("{$this->baseUrl}/v1/oauth2/token");
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST           => true,
            CURLOPT_USERPWD        => "{$this->clientId}:{$this->secret}",
            CURLOPT_POSTFIELDS     => 'grant_type=client_credentials',
            CURLOPT_HTTPHEADER     => ['Accept: application/json'],
        ]);
        $raw = curl_exec($ch);
        $res = json_decode($raw, true);
        curl_close($ch);

        // DEBUG TEMPORAL
        error_log('PAYPAL TOKEN RESPUESTA: ' . $raw);

        return $res['access_token'] ?? '';
    }

    public function crearOrden(float $total): array
    {
        $token = $this->getAccessToken();

        if (!$token) {
            error_log('PAYPAL ERROR: No se obtuvo access token');
            return [];
        }

        $ch = curl_init("{$this->baseUrl}/v2/checkout/orders");
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => json_encode([
                'intent' => 'CAPTURE',
                'purchase_units' => [[
                    'amount' => [
                        'currency_code' => 'EUR',
                        'value'         => number_format($total, 2, '.', ''),
                    ]
                ]],
                'application_context' => [
                    'return_url' => 'http://localhost' . URL_BASE . '/pago/exito',
                    'cancel_url' => 'http://localhost' . URL_BASE . '/pago/cancelado',
                ]
            ]),
            CURLOPT_HTTPHEADER => [
                "Authorization: Bearer $token",
                'Content-Type: application/json',
            ],
        ]);
        $raw = curl_exec($ch);
        $res = json_decode($raw, true);
        curl_close($ch);

        // DEBUG TEMPORAL
        error_log('PAYPAL ORDEN RESPUESTA: ' . $raw);

        return $res ?? [];
    }

    public function capturarOrden(string $orderId): array
    {
        $token = $this->getAccessToken();

        $ch = curl_init("{$this->baseUrl}/v2/checkout/orders/{$orderId}/capture");
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST           => true,
            CURLOPT_HTTPHEADER     => [
                "Authorization: Bearer $token",
                'Content-Type: application/json',
            ],
        ]);
        $raw = curl_exec($ch);
        $res = json_decode($raw, true);
        curl_close($ch);

        // DEBUG TEMPORAL
        error_log('PAYPAL CAPTURA RESPUESTA: ' . $raw);

        return $res ?? [];
    }
}

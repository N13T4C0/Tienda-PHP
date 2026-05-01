<?php

/**
 * Cliente OAuth 2.0 para Google sin dependencias externas.
 * Usa curl nativo de PHP.
 */
class GoogleOAuth
{
    private const AUTH_URL  = 'https://accounts.google.com/o/oauth2/v2/auth';
    private const TOKEN_URL = 'https://oauth2.googleapis.com/token';
    private const USER_URL  = 'https://www.googleapis.com/oauth2/v3/userinfo';

    public function __construct(
        private string $clientId,
        private string $clientSecret,
        private string $redirectUri
    ) {}

    /** Genera la URL de consentimiento de Google */
    public function getAuthUrl(string $state): string
    {
        $params = http_build_query([
            'client_id'             => $this->clientId,
            'redirect_uri'          => $this->redirectUri,
            'response_type'         => 'code',
            'scope'                 => 'openid email profile',
            'access_type'           => 'online',
            'state'                 => $state,
            'prompt'                => 'select_account',
        ]);

        return self::AUTH_URL . '?' . $params;
    }

    /**
     * Intercambia el code por un access_token
     * @return array<string,mixed>
     */
    public function intercambiarCode(string $code): array
    {
        return $this->post(self::TOKEN_URL, [
            'code'          => $code,
            'client_id'     => $this->clientId,
            'client_secret' => $this->clientSecret,
            'redirect_uri'  => $this->redirectUri,
            'grant_type'    => 'authorization_code',
        ]);
    }

    /**
     * Obtiene los datos del usuario con el access_token
     * @return array<string,mixed>
     */
    public function getUserInfo(string $accessToken): array
    {
        return $this->get(self::USER_URL, $accessToken);
    }

    /** @return array<string,mixed> */
    private function post(string $url, array $data): array
    {
        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => http_build_query($data),
            CURLOPT_HTTPHEADER     => ['Content-Type: application/x-www-form-urlencoded'],
            CURLOPT_SSL_VERIFYPEER => true,
        ]);
        $response = curl_exec($ch);
        curl_close($ch);

        return json_decode($response, true) ?? [];
    }

    /** @return array<string,mixed> */
    private function get(string $url, string $token): array
    {
        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER     => ['Authorization: Bearer ' . $token],
            CURLOPT_SSL_VERIFYPEER => true,
        ]);
        $response = curl_exec($ch);
        curl_close($ch);

        return json_decode($response, true) ?? [];
    }
}
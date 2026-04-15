<?php

declare(strict_types=1);

namespace AccountaBuddy\Discord;

use AccountaBuddy\Config;
use AccountaBuddy\Handlers\InteractionRouter;

class Webhook
{
    public function handle(): void
    {
        $body      = file_get_contents('php://input');
        $signature = $_SERVER['HTTP_X_SIGNATURE_ED25519'] ?? '';
        $timestamp = $_SERVER['HTTP_X_SIGNATURE_TIMESTAMP'] ?? '';

        if (!$this->verify($signature, $timestamp, $body)) {
            http_response_code(401);
            echo 'Invalid request signature';
            return;
        }

        $payload = json_decode($body, true);
        if ($payload === null) {
            http_response_code(400);
            echo 'Invalid JSON';
            return;
        }

        // PING
        if ($payload['type'] === Types::PING) {
            $this->respond(['type' => Types::PONG]);
            return;
        }

        header('Content-Type: application/json');

        $router = new InteractionRouter($payload);
        $response = $router->dispatch();

        $this->respond($response);
    }

    private function verify(string $signature, string $timestamp, string $body): bool
    {
        if ($signature === '' || $timestamp === '') {
            return false;
        }
        try {
            return sodium_crypto_sign_verify_detached(
                hex2bin($signature),
                $timestamp . $body,
                hex2bin(Config::publicKey())
            );
        } catch (\Throwable) {
            return false;
        }
    }

    private function respond(array $data): void
    {
        header('Content-Type: application/json');
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
    }
}

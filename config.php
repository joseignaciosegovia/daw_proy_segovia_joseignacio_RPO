<?php

require_once __DIR__ . '/vendor/autoload.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$dotenv = Dotenv\Dotenv::createUnsafeImmutable(__DIR__);
$dotenv->safeLoad();

function enviarCorreo(string $destinatario, string $asunto, string $cuerpoHtml): bool {
    $body = json_encode([
        'from'    => ['email' => 'mailtrap@example.com', 'name' => 'Reservas Moral de Calatrava'],
        'to'      => [['email' => $destinatario]],
        'subject' => $asunto,
        'html'    => $cuerpoHtml,
    ]);

    $ch = curl_init('https://sandbox.api.mailtrap.io/api/send/' . (getenv('MAILTRAP_INBOX_ID') ?: $_ENV['MAILTRAP_INBOX_ID']));
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST           => true,
        CURLOPT_POSTFIELDS     => $body,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTPHEADER     => [
            'Api-Token: ' . (getenv('MAILTRAP_TOKEN') ?: $_ENV['MAILTRAP_TOKEN']),
            'Content-Type: application/json',
        ],
    ]);

    error_log("URL: " . curl_getinfo($ch, CURLINFO_EFFECTIVE_URL));

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    error_log("HTTP: " . curl_getinfo($ch, CURLINFO_HTTP_CODE));
error_log("RESPUESTA: " . $response);

    if ($httpCode !== 200) {
        error_log("Error al enviar correo: HTTP $httpCode - $response");
        return false;
    }
    return true;
}
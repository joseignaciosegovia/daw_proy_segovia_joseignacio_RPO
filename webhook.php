<?php 
require 'vendor/autoload.php';

\Stripe\Stripe::setApiKey(getenv('STRIPE_SECRET_KEY'));

$payload   = file_get_contents('php://input');
$sigHeader = $_SERVER['HTTP_STRIPE_SIGNATURE'];
$secret    = 'whsec_TU_WEBHOOK_SECRET';

try {
    $event = \Stripe\Webhook::constructEvent($payload, $sigHeader, $secret);
} catch (\Exception $e) {
    http_response_code(400);
    exit();
}

if ($event->type === 'payment_intent.succeeded') {
    $intent = $event->data->object;
    // Actualiza tu base de datos, envía email, etc.
    error_log('Pago completado: ' . $intent->id);
}

http_response_code(200);
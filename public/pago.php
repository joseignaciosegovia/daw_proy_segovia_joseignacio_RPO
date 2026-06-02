<?php
    require_once '/../vendor/autoload.php';

    header('Content-Type: application/json');

    \Stripe\Stripe::setApiKey(getenv('STRIPE_SECRET_KEY'));

    // Si hemos recibido el pago
    if(isset($_POST['cantidad'])){
        try {
            $paymentIntent = \Stripe\PaymentIntent::create([
                'amount'   => $_POST['cantidad'],
                'currency' => 'eur',
                'automatic_payment_methods' => ['enabled' => true],
            ]);

            echo json_encode(['clientSecret' => $paymentIntent->client_secret]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => $e->getMessage()]);
        }
        exit;
    }
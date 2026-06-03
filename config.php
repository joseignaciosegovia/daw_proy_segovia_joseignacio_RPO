<?php

require_once __DIR__ . '/vendor/autoload.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$dotenv = Dotenv\Dotenv::createUnsafeImmutable(__DIR__);
$dotenv->safeLoad(); 

function enviarCorreo(string $destinatario, string $asunto, string $cuerpoHtml): bool {
    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host       = $_ENV['MAILTRAP_HOST'];
        $mail->SMTPAuth   = true;
        $mail->Username   = $_ENV['MAILTRAP_USERNAME'];
        $mail->Password   = $_ENV['MAILTRAP_PASSWORD'];
        $mail->Port       = $_ENV['MAILTRAP_PORT'];

        $mail->setFrom('reservas@moraldecalatrava.es', 'Reservas Moral de Calatrava');
        $mail->addAddress($destinatario);

        $mail->isHTML(true);
        $mail->CharSet = 'UTF-8';
        $mail->Subject = $asunto;
        $mail->Body    = $cuerpoHtml;

        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log("Error al enviar correo: {$mail->ErrorInfo}");
        return false;
    }
}
<?php
require __DIR__ . '/../phpMailer/Exception.php';
require __DIR__ . '/../phpMailer/PHPMailer.php';
require __DIR__ . '/../phpMailer/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

function enviarEmail($destinatario, $asunto, $cuerpo) {
    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com'; 
        $mail->SMTPAuth   = true;
        
        // AQUÍ CORREO A USAR
        $mail->Username   = 'srpiequiropedia4@gmail.com'; 
        
        // AQUÍ LA CLAVE DE 16 LETRAS 
        $mail->Password   = 'vnxu qjjz qubo dnza'; 
        
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        $mail->Port       = 465;

        // Quién envía el correo
        $mail->setFrom('srpiequiropedia4@gmail.com', 'srpiequiropedia');
        $mail->addAddress($destinatario);

        $mail->isHTML(true);
        $mail->Subject = $asunto;
        $mail->Body    = $cuerpo;
        $mail->CharSet = 'UTF-8';

        return $mail->send();
    } catch (Exception $e) {
        return false;
    }
}
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

// NUEVA FUNCIÓN PARA ENVIAR CORREOS CON PDF ADJUNTO
function enviarEmailConPDF($destinatario, $nombre_paciente, $asunto, $cuerpo, $pdf_en_memoria, $nombre_archivo_pdf) {
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
        $mail->setFrom('srpiequiropedia4@gmail.com', 'Sr. Pie Quiropedia');
        $mail->addAddress($destinatario, $nombre_paciente);

        // --- LA MAGIA: ADJUNTAR EL PDF ---
        $mail->addStringAttachment($pdf_en_memoria, $nombre_archivo_pdf);

        $mail->isHTML(true);
        $mail->Subject = $asunto;
        $mail->Body    = $cuerpo;
        $mail->CharSet = 'UTF-8';

        return $mail->send();
    } catch (Exception $e) {
        return false;
    }
}
?>
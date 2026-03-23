<?php
session_start();
define('FPDF_FONTPATH', '../includes/fpdf/font/');
require_once '../includes/fpdf/fpdf.php';
require_once '../includes/db.php';
require_once '../includes/mailer.php'; // Tu archivo de correos

$cedula = $_GET['cedula'] ?? '';
if (!$cedula) die("Error: Cédula no proporcionada.");

$stmt_pac = $conexion->prepare("SELECT * FROM pacientes WHERE cedula_id = ?");
$stmt_pac->execute([$cedula]);
$paciente = $stmt_pac->fetch(PDO::FETCH_ASSOC);

if (!$paciente || empty($paciente['correo'])) {
    die("<script>alert('Error: El paciente no tiene correo registrado.'); window.close();</script>");
}

$stmt_hist = $conexion->prepare("SELECT * FROM historial_clinico WHERE paciente_cedula = ? ORDER BY fecha_registro DESC");
$stmt_hist->execute([$cedula]);
$historiales = $stmt_hist->fetchAll(PDO::FETCH_ASSOC);

class PDF extends FPDF {
    function Header() {
        $this->Image('../assets/img/logo_sr_pie.png', 10, 8, 20);
        $this->SetFont('Arial', 'B', 15);
        $this->Cell(80);
        $this->Cell(30, 10, utf8_decode('SR. PIE QUIROPEDIA'), 0, 1, 'C');
        $this->SetFont('Arial', '', 10);
        $this->Cell(80);
        $this->Cell(30, 5, utf8_decode('Historial Médico del Paciente'), 0, 1, 'C');
        $this->Ln(15);
    }
    function Footer() {
        $this->SetY(-15);
        $this->SetFont('Arial', 'I', 8);
        $this->Cell(0, 10, utf8_decode('Página ') . $this->PageNo() . '/{nb}', 0, 0, 'C');
    }
}

$pdf = new PDF();
$pdf->AliasNbPages();
$pdf->AddPage();

$pdf->SetFillColor(13, 110, 253); 
$pdf->SetTextColor(255, 255, 255);
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(0, 8, utf8_decode(' DATOS DEL PACIENTE'), 0, 1, 'L', true);

$pdf->SetTextColor(0, 0, 0);
$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(20, 8, 'Nombre: ', 0, 0);
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(100, 8, utf8_decode($paciente['primer_nombre'] . ' ' . $paciente['primer_apellido']), 0, 0);
$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(20, 8, utf8_decode('Cédula: '), 0, 0);
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(0, 8, $paciente['tipo_doc'] . '-' . $paciente['cedula_id'], 0, 1);
$pdf->Ln(5);

if (count($historiales) > 0) {
    foreach ($historiales as $registro) {
        $pdf->SetFillColor(230, 230, 230);
        $pdf->SetFont('Arial', 'B', 11);
        $pdf->Cell(0, 8, ' Consulta del: ' . date('d/m/Y h:i A', strtotime($registro['fecha_registro'])), 1, 1, 'L', true);
        
        $pdf->SetFont('Arial', 'B', 10);
        $pdf->Cell(45, 6, 'Causa de la Cita:', 0, 0);
        $pdf->SetFont('Arial', '', 10);
        $pdf->MultiCell(0, 6, utf8_decode($registro['motivo_consulta'] ?? 'No registrada'));
        
        $pdf->SetFont('Arial', 'B', 10);
        $pdf->Cell(45, 6, utf8_decode('Diagnóstico:'), 0, 0);
        $pdf->SetFont('Arial', '', 10);
        $pdf->MultiCell(0, 6, utf8_decode($registro['diagnostico'] ?? 'No registrado'));

        $pdf->SetFont('Arial', 'B', 10);
        $pdf->Cell(45, 6, 'Tratamiento:', 0, 0);
        $pdf->SetFont('Arial', '', 10);
        $pdf->MultiCell(0, 6, utf8_decode($registro['tratamiento'] ?? 'No registrado'));

        $pdf->SetFont('Arial', 'B', 10);
        $pdf->Cell(45, 6, 'Observaciones:', 0, 0);
        $pdf->SetFont('Arial', '', 10);
        $pdf->MultiCell(0, 6, utf8_decode($registro['observaciones'] ?? 'Ninguna'));

        $pdf->Ln(5);
    }
} else {
    $pdf->SetFont('Arial', 'I', 11);
    $pdf->Cell(0, 10, 'El paciente no tiene consultas registradas.', 0, 1, 'C');
}

// GUARDAR EN MEMORIA (S) PARA EL CORREO
$pdf_en_memoria = $pdf->Output('S'); 

// ENVIAR POR CORREO
$asunto = 'Su Historial Médico - Sr. Pie Quiropedia';
$cuerpo = 'Hola <b>' . $paciente['primer_nombre'] . '</b>,<br><br>Adjunto le enviamos una copia de su Historial Médico detallado en nuestra clínica.<br><br>Ante cualquier duda, estamos a su orden.<br><br><b>Sr. Pie Quiropedia</b>';
$nombre_archivo = 'Historial_Medico_' . $paciente['cedula_id'] . '.pdf';

$envio_exitoso = enviarEmailConPDF(
    $paciente['correo'], 
    $paciente['primer_nombre'] . ' ' . $paciente['primer_apellido'], 
    $asunto, 
    $cuerpo, 
    $pdf_en_memoria, 
    $nombre_archivo
);

if ($envio_exitoso) {
    echo "<script>alert('✅ Historial médico enviado exitosamente al correo.'); window.close();</script>";
} else {
    echo "<script>alert('❌ Error al enviar el correo. Revisa tu configuración.'); window.close();</script>";
}
?>
<?php
session_start();
define('FPDF_FONTPATH', '../includes/fpdf/font/');
require_once '../includes/fpdf/fpdf.php';
require_once '../includes/db.php';

$cedula = $_GET['cedula'] ?? '';
if (!$cedula) die("Error: Cédula no proporcionada.");

// Obtener datos del paciente
$stmt_pac = $conexion->prepare("SELECT * FROM pacientes WHERE cedula_id = ?");
$stmt_pac->execute([$cedula]);
$paciente = $stmt_pac->fetch(PDO::FETCH_ASSOC);

if (!$paciente) die("Paciente no encontrado.");

// Obtener los registros del historial (Con el JOIN para traer el Servicio Aplicado)
$stmt_hist = $conexion->prepare("
    SELECT h.*, s.nombre AS servicio_nombre
    FROM historial_clinico h 
    LEFT JOIN citas c ON h.cita_id = c.cita_id
    LEFT JOIN servicios s ON c.servicio_id = s.servicio_id
    WHERE h.paciente_cedula = ? 
    ORDER BY h.fecha_registro DESC
");
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

// Barra azul de datos del paciente
$pdf->SetFillColor(13, 110, 253); 
$pdf->SetTextColor(255, 255, 255);
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(0, 8, utf8_decode(' DATOS DEL PACIENTE'), 0, 1, 'L', true);

// Datos del paciente (Línea 1)
$pdf->SetTextColor(0, 0, 0);
$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(20, 8, 'Nombre: ', 0, 0);
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(100, 8, utf8_decode($paciente['primer_nombre'] . ' ' . $paciente['primer_apellido']), 0, 0);
$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(20, 8, utf8_decode('Cédula: '), 0, 0);
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(0, 8, $paciente['tipo_doc'] . '-' . $paciente['cedula_id'], 0, 1);

// Datos del paciente (Línea 2: Instagram y Diabético)
$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(20, 8, 'Instagram: ', 0, 0);
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(100, 8, utf8_decode($paciente['instagram'] ?? 'N/A'), 0, 0);

$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(20, 8, utf8_decode('Diabético: '), 0, 0);

// Lógica de colores para Diabético
$pdf->SetFont('Arial', 'B', 10);
if(isset($paciente['diabetico']) && $paciente['diabetico'] == 'Si') {
    $pdf->Cell(0, 8, utf8_decode('SÍ (Precaución)'), 0, 1);
} else {
    $pdf->Cell(0, 8, 'NO', 0, 1);
}
$pdf->SetTextColor(0, 0, 0); // Restaurar a color negro
$pdf->Ln(5);


// Recorrer la bitácora médica
if (count($historiales) > 0) {
    foreach ($historiales as $registro) {
        // Cabecera de la consulta
        $pdf->SetFillColor(230, 230, 230);
        $pdf->SetFont('Arial', 'B', 11);
        $pdf->Cell(0, 8, ' Consulta del: ' . date('d/m/Y h:i A', strtotime($registro['fecha_registro'])), 1, 1, 'L', true);
        
        // Servicio Aplicado
        $pdf->SetFont('Arial', 'B', 10);
        $pdf->Cell(45, 6, 'Servicio Aplicado:', 0, 0);
        $pdf->SetFont('Arial', '', 10);
        $pdf->MultiCell(0, 6, utf8_decode($registro['servicio_nombre'] ?? 'No registrado'));

        // Causa
        $pdf->SetFont('Arial', 'B', 10);
        $pdf->Cell(45, 6, 'Causa de la Cita:', 0, 0);
        $pdf->SetFont('Arial', '', 10);
        $pdf->MultiCell(0, 6, utf8_decode($registro['motivo_consulta'] ?? 'No registrada'));
        
        // Diagnóstico
        $pdf->SetFont('Arial', 'B', 10);
        $pdf->Cell(45, 6, utf8_decode('Diagnóstico:'), 0, 0);
        $pdf->SetFont('Arial', '', 10);
        $pdf->MultiCell(0, 6, utf8_decode($registro['diagnostico'] ?? 'No registrado'));

        // Tratamiento
        $pdf->SetFont('Arial', 'B', 10);
        $pdf->Cell(45, 6, 'Tratamiento:', 0, 0);
        $pdf->SetFont('Arial', '', 10);
        $pdf->MultiCell(0, 6, utf8_decode($registro['tratamiento'] ?? 'No registrado'));

        // Observaciones
        $pdf->SetFont('Arial', 'B', 10);
        $pdf->Cell(45, 6, 'Observaciones:', 0, 0);
        $pdf->SetFont('Arial', '', 10);
        $pdf->MultiCell(0, 6, utf8_decode($registro['observaciones'] ?? 'Ninguna'));

        $pdf->Ln(5); // Espacio entre consultas
    }
} else {
    $pdf->SetFont('Arial', 'I', 11);
    $pdf->Cell(0, 10, 'El paciente no tiene consultas registradas.', 0, 1, 'C');
}

// Visualizar el PDF en el navegador (Usando la 'I' que configuramos antes)
$pdf->Output('I', 'Historial_Medico_' . $paciente['cedula_id'] . '.pdf');
?>
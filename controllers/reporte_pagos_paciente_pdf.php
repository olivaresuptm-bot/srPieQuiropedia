<?php
define('FPDF_FONTPATH', '../includes/fpdf/font/');

require_once '../includes/fpdf/fpdf.php'; 
require_once '../includes/db.php';

$cedula = $_GET['cedula'] ?? '';

if (!$cedula) {
    die("Cédula no proporcionada.");
}

// 1. Obtener datos del paciente
$stmt_pac = $conexion->prepare("SELECT primer_nombre, primer_apellido, cedula_id FROM pacientes WHERE cedula_id = ?");
$stmt_pac->execute([$cedula]);
$paciente = $stmt_pac->fetch(PDO::FETCH_ASSOC);

// 2. Obtener historial de pagos
$sql = "SELECT pg.pago_id, pg.monto, pg.metodo_pago, pg.fecha_pago, s.nombre as servicio
        FROM pagos pg
        JOIN citas c ON pg.cita_id = c.cita_id
        JOIN servicios s ON c.servicio_id = s.servicio_id
        WHERE c.paciente_cedula = ?
        ORDER BY pg.fecha_pago DESC";
$stmt_pagos = $conexion->prepare($sql);
$stmt_pagos->execute([$cedula]);
$pagos = $stmt_pagos->fetchAll(PDO::FETCH_ASSOC);

// 3. Crear el PDF
$pdf = new FPDF();
$pdf->AddPage();
$pdf->SetFont('Arial', 'B', 16);

// Logo (ajusta la ruta según tu carpeta)
// $pdf->Image('../assets/img/logo_sr_pie.png', 10, 8, 33); 

$pdf->Cell(0, 10, utf8_decode('SR. PIE QUIROPEDIA'), 0, 1, 'C');
$pdf->SetFont('Arial', '', 12);
$pdf->Cell(0, 10, utf8_decode('Reporte de Pagos del Paciente'), 0, 1, 'C');
$pdf->Ln(10);

// Info del Paciente
$pdf->SetFont('Arial', 'B', 11);
$pdf->Cell(0, 10, utf8_decode('Paciente: ') . utf8_decode($paciente['primer_nombre'] . ' ' . $paciente['primer_apellido']), 0, 1);
$pdf->Cell(0, 5, utf8_decode('Cédula: ') . $paciente['cedula_id'], 0, 1);
$pdf->Ln(5);

// Tabla de Pagos
$pdf->SetFillColor(232, 232, 232);
$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(30, 7, utf8_decode('Recibo #'), 1, 0, 'C', true);
$pdf->Cell(40, 7, 'Fecha', 1, 0, 'C', true);
$pdf->Cell(60, 7, 'Servicio', 1, 0, 'C', true);
$pdf->Cell(30, 7, 'Metodo', 1, 0, 'C', true);
$pdf->Cell(30, 7, 'Monto ($)', 1, 1, 'C', true);

$pdf->SetFont('Arial', '', 10);
foreach ($pagos as $p) {
    $pdf->Cell(30, 6, str_pad($p['pago_id'], 5, '0', STR_PAD_LEFT), 1, 0, 'C');
    $pdf->Cell(40, 6, date('d/m/Y', strtotime($p['fecha_pago'])), 1, 0, 'C');
    $pdf->Cell(60, 6, utf8_decode($p['servicio']), 1, 0, 'L');
    $pdf->Cell(30, 6, ucfirst($p['metodo_pago']), 1, 0, 'C');
    $pdf->Cell(30, 6, number_format($p['monto'], 2), 1, 1, 'R');
}

$pdf->Output('I', 'Historial_Pagos_' . $cedula . '.pdf');
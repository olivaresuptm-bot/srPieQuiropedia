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

if (!$paciente) {
    die("Paciente no encontrado.");
}

// 2. Obtener historial de pagos
$sql = "SELECT pg.pago_id, pg.monto, pg.metodo_pago, pg.fecha_pago, pg.referencia, s.nombre as servicio
        FROM pagos pg
        JOIN citas c ON pg.cita_id = c.cita_id
        JOIN servicios s ON c.servicio_id = s.servicio_id
        WHERE c.paciente_cedula = ?
        ORDER BY pg.fecha_pago DESC";
$stmt_pagos = $conexion->prepare($sql);
$stmt_pagos->execute([$cedula]);
$pagos = $stmt_pagos->fetchAll(PDO::FETCH_ASSOC);

// 3. Crear el PDF
$pdf = new FPDF('P', 'mm', 'Letter'); // Usamos tamaño carta para que quepa todo el historial
$pdf->AddPage();
$pdf->SetMargins(15, 15, 15);

// --- ENCABEZADO ESTILO TICKET ---
$pdf->Image('../assets/img/logo_sr_pie.png', 15, 12, 25);
$pdf->SetFont('Arial', 'B', 18);
$pdf->SetTextColor(13, 110, 253); // Azul Bootstrap
$pdf->Cell(0, 10, utf8_decode('QUIROPEDIA SR. PIE. C.A. '), 0, 1, 'C');

$pdf->SetFont('Arial', '', 10);
$pdf->SetTextColor(100, 100, 100);
$pdf->Cell(0, 5, utf8_decode('RIF: J-41230047-4 | Tel: (0274) 266-6818 / (0414) 735-9726'), 0, 1, 'C');
$pdf->Cell(0, 5, utf8_decode('Mérida, Venezuela'), 0, 1, 'C');
$pdf->Cell(0, 5, utf8_decode('C.C. Las Tapias Nivel 1 Local 57'), 0, 1, 'C');
$pdf->Cell(0, 5, utf8_decode('Quiropediasrpie@gmail.com'), 0, 1, 'C');
$pdf->Ln(5);

$pdf->SetDrawColor(13, 110, 253);
$pdf->SetLineWidth(0.5);
$pdf->Line(15, $pdf->GetY(), 200, $pdf->GetY());
$pdf->Ln(7);

// --- INFO PACIENTE ---
$pdf->SetTextColor(0, 0, 0);
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(0, 7, utf8_decode('REPORTE HISTÓRICO DE PAGOS'), 0, 1, 'L');

$pdf->SetFont('Arial', '', 11);
$pdf->Cell(40, 7, utf8_decode('Paciente:'), 0, 0);
$pdf->SetFont('Arial', 'B', 11);
$pdf->Cell(0, 7, utf8_decode($paciente['primer_nombre'] . ' ' . $paciente['primer_apellido']), 0, 1);

$pdf->SetFont('Arial', '', 11);
$pdf->Cell(40, 7, utf8_decode('Cédula:'), 0, 0);
$pdf->SetFont('Arial', 'B', 11);
$pdf->Cell(0, 7, $paciente['cedula_id'], 0, 1);
$pdf->Ln(8);

// --- TABLA DE PAGOS ESTILIZADA ---
$pdf->SetFillColor(13, 110, 253);
$pdf->SetTextColor(255, 255, 255);
$pdf->SetFont('Arial', 'B', 10);

$pdf->Cell(25, 10, utf8_decode('Recibo #'), 1, 0, 'C', true);
$pdf->Cell(35, 10, 'Fecha Pago', 1, 0, 'C', true);
$pdf->Cell(55, 10, 'Servicio', 1, 0, 'C', true);
$pdf->Cell(40, 10, utf8_decode('Método'), 1, 0, 'C', true);
$pdf->Cell(30, 10, 'Monto ($)', 1, 1, 'C', true);

$pdf->SetTextColor(0, 0, 0);
$pdf->SetFont('Arial', '', 10);
$pdf->SetFillColor(245, 245, 245);

$total_acumulado = 0;
$fill = false;

foreach ($pagos as $p) {
    // Lógica de nombres de método como el ticket
    $texto_metodo = match($p['metodo_pago']) {
        'efectivo'    => 'Efectivo $',
        'efectivo_bs' => 'Efectivo Bs',
        'pago_movil'  => 'Pago Movil',
        'punto'       => 'Punto de Venta',
        default       => ucfirst(str_replace('_', ' ', $p['metodo_pago']))
    };

    $pdf->Cell(25, 8, '#' . str_pad($p['pago_id'], 5, '0', STR_PAD_LEFT), 1, 0, 'C', $fill);
    $pdf->Cell(35, 8, date('d/m/Y', strtotime($p['fecha_pago'])), 1, 0, 'C', $fill);
    $pdf->Cell(55, 8, utf8_decode($p['servicio']), 1, 0, 'L', $fill);
    $pdf->Cell(40, 8, utf8_decode($texto_metodo), 1, 0, 'C', $fill);
    $pdf->SetFont('Arial', 'B', 10);
    $pdf->Cell(30, 8, '$' . number_format($p['monto'], 2), 1, 1, 'R', $fill);
    $pdf->SetFont('Arial', '', 10);
    
    $total_acumulado += $p['monto'];
    $fill = !$fill; // Alternar color de fila
}

// --- RESUMEN FINAL ---
$pdf->Ln(5);
$pdf->SetFont('Arial', 'B', 11);
$pdf->Cell(125, 10, '', 0, 0);
$pdf->Cell(30, 10, 'TOTAL:', 1, 0, 'C', true); // Reutilizamos el azul para el total
$pdf->SetTextColor(0, 0, 0);
$pdf->Cell(30, 10, '$' . number_format($total_acumulado, 2), 1, 1, 'R');

// --- PIE DE PÁGINA ---
$pdf->Ln(20);
$pdf->SetFont('Arial', 'I', 9);
$pdf->SetTextColor(150, 150, 150);
$pdf->Cell(0, 5, utf8_decode('Este documento es un reporte informativo generado por el sistema Sr. Pie Quiropedia.'), 0, 1, 'C');
$pdf->Cell(0, 5, 'Fecha de impresion: ' . date('d/m/Y h:i A'), 0, 1, 'C');

$pdf->Output('I', 'Historial_Pagos_' . $cedula . '.pdf');
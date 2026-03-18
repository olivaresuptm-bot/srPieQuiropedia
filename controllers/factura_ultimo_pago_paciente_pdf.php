<?php
define('FPDF_FONTPATH', '../includes/fpdf/font/');

require_once '../includes/fpdf/fpdf.php';
require_once '../includes/db.php';

$cedula = $_GET['cedula'] ?? '';

if (!$cedula) {
    die("Error: Cédula no proporcionada.");
}

// 1. Datos del paciente
$stmt_pac = $conexion->prepare("SELECT primer_nombre, primer_apellido, cedula_id FROM pacientes WHERE cedula_id = ?");
$stmt_pac->execute([$cedula]);
$paciente = $stmt_pac->fetch(PDO::FETCH_ASSOC);

if (!$paciente) {
    die("Error: Paciente no encontrado.");
}

// 2. Ultimo pago realizado
$sql = "SELECT pg.pago_id, pg.monto, pg.tasa_bcv, pg.metodo_pago, pg.fecha_pago, pg.referencia, 
               s.nombre as servicio
        FROM pagos pg
        JOIN citas c ON pg.cita_id = c.cita_id
        JOIN servicios s ON c.servicio_id = s.servicio_id
        WHERE c.paciente_cedula = ?
        ORDER BY pg.fecha_pago DESC, pg.pago_id DESC
        LIMIT 1";

$stmt_pago = $conexion->prepare($sql);
$stmt_pago->execute([$cedula]);
$pago = $stmt_pago->fetch(PDO::FETCH_ASSOC);

if (!$pago) {
    die("Error: Este paciente no tiene pagos registrados.");
}

// 3. Iniciar generación del PDF
$pdf = new FPDF('P', 'mm', array(80, 150)); // Formato tipo ticket (80mm ancho)
$pdf->AddPage();
$pdf->SetMargins(5, 5, 5);

// --- Encabezado ---
$pdf->Image('../assets/img/logo_sr_pie.png', 30, 5, 20); 
$pdf->Ln(15); // Espacio ajustado para que no choque con el texto

$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(0, 5, utf8_decode('SR. PIE QUIROPEDIA'), 0, 1, 'C');
$pdf->SetFont('Arial', '', 8);
$pdf->Cell(0, 5, utf8_decode('RIF: J-41230047-4'), 0, 1, 'C');
$pdf->Cell(0, 5, utf8_decode('Tel: (0274) 266-6818 / (0414) 735-9726'), 0, 1, 'C');
$pdf->Cell(0, 5, utf8_decode('Mérida, Venezuela'), 0, 1, 'C');
$pdf->Cell(0, 5, utf8_decode('C.C. Las Tapias Nivel 1 Local 57'), 0, 1, 'C');
$pdf->Cell(0, 5, utf8_decode('Quiropediasrpie@gmail.com'), 0, 1, 'C');
$pdf->Ln(5);

// --- Datos del Recibo ---
$pdf->SetFont('Arial', 'B', 9);
$pdf->Cell(0, 5, utf8_decode('RECIBO DE PAGO #') . str_pad($pago['pago_id'], 5, '0', STR_PAD_LEFT), 0, 1, 'L');
$pdf->SetFont('Arial', '', 9);
$pdf->Cell(0, 5, 'Fecha: ' . date('d/m/Y h:i A', strtotime($pago['fecha_pago'])), 0, 1, 'L');
$pdf->Ln(2);

// --- Datos del Paciente ---
$pdf->Line(5, $pdf->GetY(), 75, $pdf->GetY());
$pdf->Ln(2);
$pdf->Cell(0, 5, utf8_decode('Paciente: ' . $paciente['primer_nombre'] . ' ' . $paciente['primer_apellido']), 0, 1, 'L');
$pdf->Cell(0, 5, utf8_decode('Cédula: ' . $paciente['cedula_id']), 0, 1, 'L');
$pdf->Ln(2);
$pdf->Line(5, $pdf->GetY(), 75, $pdf->GetY());
$pdf->Ln(2);

// --- Detalle del Servicio y Montos ---
$pdf->SetFont('Arial', 'B', 9);
$pdf->Cell(45, 5, utf8_decode('Descripción'), 0, 0, 'L');
$pdf->Cell(25, 5, 'Total', 0, 1, 'R');
$pdf->SetFont('Arial', '', 9);

$pdf->Cell(45, 5, utf8_decode($pago['servicio']), 0, 0, 'L');
$pdf->Cell(25, 5, '$' . number_format($pago['monto'], 2), 0, 1, 'R');

// Lógica de Conversión a Bolívares
if (!empty($pago['tasa_bcv']) && $pago['tasa_bcv'] > 0) {
    $monto_bs = $pago['monto'] * $pago['tasa_bcv'];
    $pdf->SetFont('Arial', 'I', 8); 
    $pdf->Cell(45, 4, utf8_decode('Tasa BCV: Bs. ' . number_format($pago['tasa_bcv'], 2)), 0, 0, 'L');
    $pdf->SetFont('Arial', 'B', 9);
    $pdf->Cell(25, 4, 'Bs. ' . number_format($monto_bs, 2), 0, 1, 'R');
}
$pdf->Ln(2);

// --- Método de Pago ---
$texto_metodo = match($pago['metodo_pago']) {
    'efectivo'    => 'Efectivo $',
    'efectivo_bs' => 'Efectivo Bs',
    'pago_movil'  => 'Pago Móvil',
    'punto'       => 'Punto de Venta',
    default       => ucfirst(str_replace('_', ' ', $pago['metodo_pago']))
};

$pdf->SetFont('Arial', 'B', 9);
$pdf->Cell(40, 5, utf8_decode('Método de Pago:'), 0, 0, 'L');
$pdf->SetFont('Arial', '', 9);
$pdf->Cell(30, 5, utf8_decode($texto_metodo), 0, 1, 'R');

// La referencia 
if (!empty($pago['referencia'])) {
    $pdf->Cell(40, 5, utf8_decode('Referencia:'), 0, 0, 'L');
    $pdf->Cell(30, 5, '#' . $pago['referencia'], 0, 1, 'R');
}

// --- Pie de Página ---
$pdf->Ln(5);
$pdf->SetFont('Arial', 'I', 8);
$pdf->Cell(0, 5, utf8_decode('¡Gracias por confiar en Sr. Pie!'), 0, 1, 'C');
$pdf->Cell(0, 5, utf8_decode('Conserve su recibo.'), 0, 1, 'C');

// IMPORTANTE: Output debe ser siempre la última instrucción
$pdf->Output('I', 'Recibo_' . str_pad($pago['pago_id'], 5, '0', STR_PAD_LEFT) . '.pdf');
<?php
session_start();
define('FPDF_FONTPATH', '../includes/fpdf/font/');
require_once '../includes/fpdf/fpdf.php';
require_once '../includes/db.php';

$cedula = isset($_GET['cedula']) ? $_GET['cedula'] : '';

if (!$cedula) {
    die("Error: Cédula no proporcionada.");
}

// 1. OBTENER DATOS DEL QUIROPEDISTA
$stmt_quiro = $conexion->prepare("SELECT * FROM usuarios WHERE cedula_id = ?");
$stmt_quiro->execute([$cedula]);
$quiro = $stmt_quiro->fetch(PDO::FETCH_ASSOC);

if (!$quiro) {
    die("Error: Quiropedista no encontrado.");
}

// 2. OBTENER EL ÚLTIMO LOTE DE SERVICIOS PAGADOS (estado_comision = 1)
$stmt_servicios = $conexion->prepare("
    SELECT 
        c.fecha AS fecha_cita,
        pa.primer_nombre, pa.primer_apellido,
        s.nombre AS servicio_nombre, 
        p.monto AS subtotal_usd,
        p.tasa_bcv AS tasa_del_dia,
        s.comision_porcentaje
    FROM pagos p
    INNER JOIN citas c ON p.cita_id = c.cita_id
    INNER JOIN servicios s ON c.servicio_id = s.servicio_id
    INNER JOIN pacientes pa ON c.paciente_cedula = pa.cedula_id
    WHERE c.quiropedista_cedula = ? 
    AND c.estado_comision = 1 
    AND c.estatus = 'atendida'
    ORDER BY c.fecha DESC
    LIMIT 15
");
$stmt_servicios->execute([$cedula]);
$servicios = $stmt_servicios->fetchAll(PDO::FETCH_ASSOC);

if (empty($servicios)) {
    echo "<script>alert('Este quiropedista no tiene pagos de comisiones registrados anteriormente.'); window.close();</script>";
    exit;
}

// 3. GENERAR PDF
$pdf = new FPDF('P', 'mm', 'Letter');
$pdf->AddPage();
$pdf->SetMargins(15, 15, 15);

// --- ENCABEZADO ESTILO TICKET/FACTURA ---
$pdf->Image('../assets/img/logo_sr_pie.png', 15, 12, 25);
$pdf->SetFont('Arial', 'B', 18);
$pdf->SetTextColor(13, 110, 253);
$pdf->Cell(0, 10, utf8_decode('QUIROPEDIA SR. PIE. C.A. '), 0, 1, 'C');

$pdf->SetFont('Arial', '', 10);
$pdf->SetTextColor(100, 100, 100);
$pdf->Cell(0, 5, utf8_decode('RIF: J-41230047-4 | Tel: (0274) 266-6818 / (0414) 735-9726'), 0, 1, 'C');
$pdf->Cell(0, 5, utf8_decode('Mérida, Venezuela'), 0, 1, 'C');
$pdf->Cell(0, 5, utf8_decode('C.C. Las Tapias Nivel 1 Local 57'), 0, 1, 'C');
$pdf->Ln(5);

$pdf->SetDrawColor(13, 110, 253);
$pdf->SetLineWidth(0.5);
$pdf->Line(15, $pdf->GetY(), 200, $pdf->GetY());
$pdf->Ln(7);

// --- INFO QUIROPEDISTA ---
$pdf->SetTextColor(0, 0, 0);
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(0, 7, utf8_decode('COMPROBANTE HISTORIAL DE COMISIONES'), 0, 1, 'L');

$pdf->SetFont('Arial', '', 11);
$pdf->Cell(35, 7, utf8_decode('Especialista:'), 0, 0);
$pdf->SetFont('Arial', 'B', 11);
$pdf->Cell(0, 7, utf8_decode($quiro['primer_nombre'] . ' ' . $quiro['primer_apellido']), 0, 1);

$pdf->SetFont('Arial', '', 11);
$pdf->Cell(35, 7, utf8_decode('Cédula:'), 0, 0);
$pdf->SetFont('Arial', 'B', 11);
$pdf->Cell(0, 7, $quiro['cedula_id'], 0, 1);

$pdf->SetFont('Arial', '', 11);
$pdf->Cell(35, 7, utf8_decode('Fecha Emisión:'), 0, 0);
$pdf->SetFont('Arial', 'B', 11);
$pdf->Cell(0, 7, date('d/m/Y h:i A'), 0, 1);
$pdf->Ln(8);

// --- TABLA DE SERVICIOS (CON COLUMNA BS) ---
$pdf->SetFillColor(13, 110, 253);
$pdf->SetTextColor(255, 255, 255);
$pdf->SetFont('Arial', 'B', 9);

// Medidas de columnas que suman 185mm (Ancho disponible)
$w_fecha = 22;
$w_pac = 40;
$w_serv = 48;
$w_com = 15;
$w_usd = 30;
$w_bs = 30;

$pdf->Cell($w_fecha, 10, 'Fecha', 1, 0, 'C', true);
$pdf->Cell($w_pac, 10, 'Paciente', 1, 0, 'C', true);
$pdf->Cell($w_serv, 10, 'Servicio', 1, 0, 'C', true);
$pdf->Cell($w_com, 10, '%', 1, 0, 'C', true);
$pdf->Cell($w_usd, 10, 'Pago ($)', 1, 0, 'C', true);
$pdf->Cell($w_bs, 10, 'Pago (Bs)', 1, 1, 'C', true);

$pdf->SetTextColor(0, 0, 0);
$pdf->SetFont('Arial', '', 9);
$pdf->SetFillColor(245, 245, 245);
$fill = false;

$total_comision_usd = 0;
$total_comision_bs = 0;

foreach ($servicios as $s) {
    // Cálculos
    $comision_usd = $s['subtotal_usd'] * ($s['comision_porcentaje'] / 100);
    $comision_bs = $comision_usd * $s['tasa_del_dia'];
    
    $total_comision_usd += $comision_usd;
    $total_comision_bs += $comision_bs;
    
    $nombre_paciente = $s['primer_nombre'] . ' ' . $s['primer_apellido'];

    // Celdas de datos
    $pdf->Cell($w_fecha, 8, date('d/m/y', strtotime($s['fecha_cita'])), 1, 0, 'C', $fill);
    $pdf->Cell($w_pac, 8, utf8_decode(substr($nombre_paciente, 0, 20)), 1, 0, 'L', $fill);
    $pdf->Cell($w_serv, 8, utf8_decode(substr($s['servicio_nombre'], 0, 25)), 1, 0, 'L', $fill);
    $pdf->Cell($w_com, 8, $s['comision_porcentaje'] . '%', 1, 0, 'C', $fill);
    
    $pdf->SetFont('Arial', 'B', 9);
    $pdf->Cell($w_usd, 8, '$' . number_format($comision_usd, 2), 1, 0, 'R', $fill);
    $pdf->Cell($w_bs, 8, number_format($comision_bs, 2, ',', '.') . ' Bs', 1, 1, 'R', $fill);
    $pdf->SetFont('Arial', '', 9);
    
    $fill = !$fill;
}



// --- PIE DE PÁGINA ---
$pdf->Ln(20);
$pdf->SetFont('Arial', 'I', 9);
$pdf->SetTextColor(150, 150, 150);
$pdf->Cell(0, 5, utf8_decode('Este documento es una copia generada por el sistema de los últimos pagos liquidados.'), 0, 1, 'C');

// 4. FORZAR DESCARGA (Si prefieres que se abra en el navegador, cambia 'D' por 'I')
$nombre_archivo = 'Pago_Semanal_' . $quiro['primer_nombre'] . '_' . date('dmY') . '.pdf';
$pdf->Output('I', $nombre_archivo);
?>
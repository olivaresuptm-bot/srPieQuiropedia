<?php
session_start();
define('FPDF_FONTPATH', '../includes/fpdf/font/');
require_once '../includes/fpdf/fpdf.php';
require_once '../includes/db.php';
require_once '../includes/mailer.php';
require_once '../includes/tasa_BCV.php';

$tasa_actual = ($tasa_bcv) ? $tasa_bcv : 0;
$cedula = isset($_GET['cedula']) ? $_GET['cedula'] : '';

if (!$cedula) die("Error: Cédula no proporcionada.");

// 1. OBTENER DATOS DEL QUIROPEDISTA
$stmt_quiro = $conexion->prepare("SELECT * FROM usuarios WHERE cedula_id = ?");
$stmt_quiro->execute([$cedula]);
$quiro = $stmt_quiro->fetch(PDO::FETCH_ASSOC);

if (!$quiro) die("Error: Quiropedista no encontrado.");

// 2. OBTENER TOTALES
$stmt_totales = $conexion->prepare("
    SELECT 
        SUM(p.monto) AS total_ventas_usd, 
        SUM(p.monto * p.tasa_bcv) AS total_ventas_bs,
        SUM(p.monto * (s.comision_porcentaje / 100)) AS total_comision_usd,
        SUM(p.monto * p.tasa_bcv * (s.comision_porcentaje / 100)) AS total_comision_bs
    FROM pagos p
    INNER JOIN citas c ON p.cita_id = c.cita_id
    INNER JOIN servicios s ON c.servicio_id = s.servicio_id
    WHERE c.quiropedista_cedula = ? 
    AND c.estado_comision = 0 
    AND c.estatus = 'atendida'
");
$stmt_totales->execute([$cedula]);
$totales = $stmt_totales->fetch(PDO::FETCH_ASSOC);

// 3. OBTENER DESGLOSE
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
    AND c.estado_comision = 0 
    AND c.estatus = 'atendida'
    ORDER BY c.fecha ASC
");
$stmt_servicios->execute([$cedula]);
$servicios = $stmt_servicios->fetchAll(PDO::FETCH_ASSOC);

if (empty($servicios)) {
    echo "<script>alert('No hay comisiones pendientes.'); window.close();</script>";
    exit;
}

// 4. GENERAR PDF
$pdf = new FPDF();
$pdf->AddPage();
$pdf->SetFont('Arial', 'B', 14);
$pdf->Cell(0, 10, utf8_decode('RECIBO DE COMISIONES - SR. PIE'), 0, 1, 'C');
$pdf->Ln(5);
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(0, 10, 'Quiropedista: ' . utf8_decode($quiro['primer_nombre'] . ' ' . $quiro['primer_apellido']), 0, 1);
$pdf->Ln(5);

// Tabla simple para no sobrecargar el servidor
foreach ($servicios as $s) {
    $comision_usd = $s['subtotal_usd'] * ($s['comision_porcentaje'] / 100);
    $pdf->Cell(30, 8, $s['fecha_cita'], 1);
    $pdf->Cell(80, 8, utf8_decode(substr($s['servicio_nombre'], 0, 40)), 1);
    $pdf->Cell(40, 8, number_format($comision_usd, 2) . ' USD', 1, 1, 'R');
}

$pdf->Ln(10);
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(110, 10, 'TOTAL A PAGAR:', 0, 0, 'R');
$pdf->Cell(40, 10, number_format($totales['total_comision_usd'], 2) . ' USD', 0, 1, 'R');

$pdf_doc = $pdf->Output('S');

// 5. ENVIAR Y LIQUIDAR
$enviado = enviarEmailConPDF($quiro['correo'], $quiro['primer_nombre'], 'Recibo Sr. Pie', 'Adjunto recibo.', $pdf_doc, 'recibo.pdf');

if ($enviado) {
    if (isset($_GET['liquidar']) && $_GET['liquidar'] == '1') {
        $upd = $conexion->prepare("UPDATE citas SET estado_comision = 1 WHERE quiropedista_cedula = ? AND estado_comision = 0 AND estatus = 'atendida'");
        $upd->execute([$cedula]);
    }
    echo "<script>alert('✅ Recibo enviado y contador reiniciado.'); if(window.opener) window.opener.location.reload(); window.close();</script>";
} else {
    echo "<script>alert('❌ Error de conexión al enviar el correo.'); window.close();</script>";
}
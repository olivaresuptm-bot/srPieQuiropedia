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

// 3. OBTENER DESGLOSE CON DATOS DEL PACIENTE
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

// 4. GENERAR PDF (CON ESTILO VISUAL DE FACTURA)
$pdf = new FPDF('P', 'mm', 'Letter');
$pdf->AddPage();
$pdf->SetMargins(15, 15, 15);

// --- ENCABEZADO ESTILO TICKET/FACTURA ---
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

// --- INFO QUIROPEDISTA ---
$pdf->SetTextColor(0, 0, 0);
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(0, 7, utf8_decode('RECIBO DE COMISIONES'), 0, 1, 'L');

$pdf->SetFont('Arial', '', 11);
$pdf->Cell(35, 7, utf8_decode('Quiropedista:'), 0, 0);
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

// --- TABLA DE SERVICIOS ESTILIZADA ---
$pdf->SetFillColor(13, 110, 253);
$pdf->SetTextColor(255, 255, 255);
$pdf->SetFont('Arial', 'B', 10);

$pdf->Cell(25, 10, 'Fecha', 1, 0, 'C', true);
$pdf->Cell(45, 10, 'Paciente', 1, 0, 'C', true);
$pdf->Cell(55, 10, 'Servicio', 1, 0, 'C', true);
$pdf->Cell(20, 10, '% Com.', 1, 0, 'C', true);
$pdf->Cell(40, 10, 'A Pagar (USD)', 1, 1, 'C', true);

$pdf->SetTextColor(0, 0, 0);
$pdf->SetFont('Arial', '', 9);
$pdf->SetFillColor(245, 245, 245);

$fill = false;

foreach ($servicios as $s) {
    $comision_usd = $s['subtotal_usd'] * ($s['comision_porcentaje'] / 100);
    $nombre_paciente = $s['primer_nombre'] . ' ' . $s['primer_apellido'];

    $pdf->Cell(25, 8, date('d/m/Y', strtotime($s['fecha_cita'])), 1, 0, 'C', $fill);
    $pdf->Cell(45, 8, utf8_decode(substr($nombre_paciente, 0, 25)), 1, 0, 'L', $fill);
    $pdf->Cell(55, 8, utf8_decode(substr($s['servicio_nombre'], 0, 30)), 1, 0, 'L', $fill);
    $pdf->Cell(20, 8, $s['comision_porcentaje'] . '%', 1, 0, 'C', $fill);
    
    $pdf->SetFont('Arial', 'B', 9);
    $pdf->Cell(40, 8, '$' . number_format($comision_usd, 2), 1, 1, 'R', $fill);
    $pdf->SetFont('Arial', '', 9);
    
    $fill = !$fill; // Alternar color de fila
}

// --- RESUMEN FINAL ---
$pdf->Ln(5);

// Total en Dólares
$pdf->SetFont('Arial', 'B', 11);
$pdf->Cell(115, 10, '', 0, 0);
$pdf->SetTextColor(0, 0, 0);
$pdf->Cell(30, 10, 'TOTAL USD:', 1, 0, 'C', true);
$pdf->SetTextColor(0, 0, 0);
$pdf->Cell(40, 10, '$' . number_format($totales['total_comision_usd'], 2), 1, 1, 'R');

// Total en Bolívares (usando la tasa en la que se cobró cada servicio)
$pdf->Cell(115, 10, '', 0, 0);
$pdf->SetTextColor(0, 0, 0);
$pdf->Cell(30, 10, 'TOTAL BS:', 1, 0, 'C', true);
$pdf->SetTextColor(0, 0, 0);
$pdf->Cell(40, 10, number_format($totales['total_comision_bs'], 2, ',', '.') . ' Bs', 1, 1, 'R');

// --- PIE DE PÁGINA ---
$pdf->Ln(20);
$pdf->SetFont('Arial', 'I', 9);
$pdf->SetTextColor(150, 150, 150);
$pdf->Cell(0, 5, utf8_decode('Este documento es un comprobante oficial de pago de comisiones generado por Sr. Pie Quiropedia.'), 0, 1, 'C');


// 5. GUARDAR EN MEMORIA (No se muestra en pantalla)
$pdf_doc = $pdf->Output('S');

// 6. ENVIAR CORREOS (AL QUIROPEDISTA Y A LA CLÍNICA)
$cuerpo_quiro = "Hola " . $quiro['primer_nombre'] . ", adjunto a este correo encontrarás el desglose de tus comisiones de la semana. ¡Excelente trabajo!";
$enviado_quiro = enviarEmailConPDF($quiro['correo'], $quiro['primer_nombre'], 'Recibo de Comisiones - Sr. Pie', $cuerpo_quiro, $pdf_doc, 'recibo_comisiones.pdf');

// Enviar copia de respaldo a la clínica
$correo_clinica = "srpiequiropedia4@gmail.com"; 
$cuerpo_clinica = "Copia automática del recibo de comisiones del especialista " . $quiro['primer_nombre'] . " " . $quiro['primer_apellido'] . ".";
$enviado_clinica = enviarEmailConPDF($correo_clinica, 'Administración', 'Copia: Recibo de Comisiones - ' . $quiro['primer_nombre'], $cuerpo_clinica, $pdf_doc, 'respaldo_comisiones.pdf');

// 7. LIQUIDAR Y CERRAR
if ($enviado_quiro) {
    // Solo si el botón mandó "liquidar=1" actualizamos la BD a pagado
    if (isset($_GET['liquidar']) && $_GET['liquidar'] == '1') {
        $upd = $conexion->prepare("UPDATE citas SET estado_comision = 1 WHERE quiropedista_cedula = ? AND estado_comision = 0 AND estatus = 'atendida'");
        $upd->execute([$cedula]);
    }
    echo "<script>alert('✅ Recibo enviado a la clínica y al quiropedista. Contador reiniciado.'); if(window.opener) window.opener.location.reload(); window.close();</script>";
} else {
    echo "<script>alert('❌ Error de conexión al enviar el correo al quiropedista.'); window.close();</script>";
}
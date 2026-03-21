<?php
// 1. Cargar librerías necesarias
define('FPDF_FONTPATH', '../includes/fpdf/font/');
require_once '../includes/fpdf/fpdf.php';
require_once '../includes/db.php';

// Cargar tu archivo de funciones de correo
require_once '../includes/mailer.php';

$cedula = $_GET['cedula'] ?? '';

if (!$cedula) {
    die("Error: Cédula no proporcionada.");
}

// 2. Obtener datos del paciente
$stmt_pac = $conexion->prepare("SELECT primer_nombre, primer_apellido, cedula_id, correo FROM pacientes WHERE cedula_id = ?");
$stmt_pac->execute([$cedula]);
$paciente = $stmt_pac->fetch(PDO::FETCH_ASSOC);

if (!$paciente || empty($paciente['correo'])) {
    die("<script>alert('Error: El paciente no tiene un correo registrado.'); window.close();</script>");
}

// 3. Obtener el último pago
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
    die("Error: No hay pagos para enviar.");
}

// =========================================================
// 4. GENERAR EL PDF
// =========================================================
$pdf = new FPDF('P', 'mm', array(80, 150));
$pdf->AddPage();
$pdf->SetMargins(5, 5, 5);

$pdf->Image('../assets/img/logo_sr_pie.png', 30, 5, 20); 
$pdf->Ln(22);

$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(0, 5, utf8_decode('SR. PIE QUIROPEDIA'), 0, 1, 'C');
$pdf->SetFont('Arial', '', 8);
$pdf->Cell(0, 5, utf8_decode('RIF: J-41230047-4'), 0, 1, 'C');
$pdf->Cell(0, 5, utf8_decode('quiropediasrpie@gmail.com'), 0, 1, 'C');
$pdf->Ln(5);

$pdf->SetFont('Arial', 'B', 9);
$pdf->Cell(0, 5, utf8_decode('RECIBO DE PAGO #') . str_pad($pago['pago_id'], 5, '0', STR_PAD_LEFT), 0, 1, 'L');
$pdf->SetFont('Arial', '', 9);
$pdf->Cell(0, 5, 'Fecha: ' . date('d/m/Y h:i A', strtotime($pago['fecha_pago'])), 0, 1, 'L');
$pdf->Ln(2);

$pdf->Line(5, $pdf->GetY(), 75, $pdf->GetY());
$pdf->Ln(2);
$pdf->Cell(0, 5, utf8_decode('Paciente: ' . $paciente['primer_nombre'] . ' ' . $paciente['primer_apellido']), 0, 1, 'L');
$pdf->Cell(0, 5, utf8_decode('Cédula: ' . $paciente['cedula_id']), 0, 1, 'L');
$pdf->Ln(2);
$pdf->Line(5, $pdf->GetY(), 75, $pdf->GetY());
$pdf->Ln(3);

$pdf->SetFont('Arial', 'B', 9);
$pdf->Cell(45, 5, utf8_decode('Descripción'), 0, 0, 'L');
$pdf->Cell(25, 5, 'Total', 0, 1, 'R');
$pdf->SetFont('Arial', '', 9);
$pdf->Cell(45, 5, utf8_decode($pago['servicio']), 0, 0, 'L');
$pdf->Cell(25, 5, '$' . number_format($pago['monto'], 2), 0, 1, 'R');

if (!empty($pago['tasa_bcv']) && $pago['tasa_bcv'] > 0) {
    $monto_bs = $pago['monto'] * $pago['tasa_bcv'];
    $pdf->SetFont('Arial', 'I', 8); 
    $pdf->Cell(45, 4, utf8_decode('Tasa BCV: Bs. ' . number_format($pago['tasa_bcv'], 2)), 0, 0, 'L');
    $pdf->SetFont('Arial', 'B', 9);
    $pdf->Cell(25, 4, 'Bs. ' . number_format($monto_bs, 2), 0, 1, 'R');
}
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
$pdf->Ln(10);
$pdf->SetFont('Arial', 'I', 8);
$pdf->Cell(0, 5, utf8_decode('¡Gracias por confiar en Sr. Pie!'), 0, 1, 'C');
$pdf->Cell(0, 5, utf8_decode('Conserve su recibo.'), 0, 1, 'C');

// Guardar en memoria
$pdf_en_memoria = $pdf->Output('S'); 

// =========================================================
// 5. ENVIAR EL CORREO USANDO TU NUEVA FUNCIÓN
// =========================================================

// Preparar los datos
$asunto = 'Su recibo de pago - Sr. Pie Quiropedia';
$cuerpo = 'Hola <b>' . $paciente['primer_nombre'] . '</b>,<br><br>Adjunto le enviamos el recibo de su último pago por nuestros servicios en la clínica.<br><br>¡Gracias por su confianza!<br><br><b>Sr. Pie Quiropedia</b>';
$nombre_archivo = 'Recibo_SrPie_000' . $pago['pago_id'] . '.pdf';

// Llamar a la nueva función definida en mailer.php
$envio_exitoso = enviarEmailConPDF(
    $paciente['correo'], 
    $paciente['primer_nombre'], 
    $asunto, 
    $cuerpo, 
    $pdf_en_memoria, 
    $nombre_archivo
);

// Comprobar el resultado
if ($envio_exitoso) {
    echo "<script>alert('✅ Recibo enviado exitosamente al correo del paciente.'); window.close();</script>";
} else {
    echo "<script>alert('❌ Error al enviar el correo. Revisa tu configuración.'); window.close();</script>";
}
?>
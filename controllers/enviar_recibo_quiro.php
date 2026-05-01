<?php
session_start();
define('FPDF_FONTPATH', '../includes/fpdf/font/');
require_once '../includes/fpdf/fpdf.php';
require_once '../includes/db.php';
require_once '../includes/mailer.php';
require_once '../includes/tasa_BCV.php';

$tasa_actual = ($tasa_bcv) ? $tasa_bcv : 0;

$cedula = $_GET['cedula'] ?? '';
if (!$cedula) die("Error: Cédula no proporcionada.");

$stmt_quiro = $conexion->prepare("SELECT * FROM usuarios WHERE cedula_id = ?");
$stmt_quiro->execute([$cedula]);
$quiro = $stmt_quiro->fetch(PDO::FETCH_ASSOC);

if (!$quiro || empty($quiro['correo'])) {
    die("<script>alert('Error: Este quiropedista no tiene un correo electrónico registrado en su perfil de usuario.'); window.close();</script>");
}

// ========================================================
// 1. OBTENER TOTALES (SOLO PENDIENTES)
// ========================================================
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

// ========================================================
// 2. DESGLOSE CRONOLÓGICO (SOLO PENDIENTES)
// ========================================================
$stmt_servicios = $conexion->prepare("
    SELECT 
        c.fecha AS fecha_cita,
        pa.primer_nombre, pa.primer_apellido,
        s.nombre AS servicio_nombre, 
        s.comision_porcentaje,
        p.monto AS subtotal_usd,
        (p.monto * p.tasa_bcv) AS subtotal_bs,
        (p.monto * (s.comision_porcentaje / 100)) AS comision_usd,
        (p.monto * p.tasa_bcv * (s.comision_porcentaje / 100)) AS comision_bs,
        p.tasa_bcv AS tasa_del_dia
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

// Verificación de seguridad: Si no hay nada que pagar, detenemos el proceso
if (empty($servicios)) {
    die("<script>alert('Aviso: Este especialista no tiene comisiones pendientes por cobrar.'); window.close();</script>");
}

class PDF extends FPDF {
    function Header() {
        $this->Image('../assets/img/logo_sr_pie.png', 10, 8, 20);
        $this->SetFont('Arial', 'B', 15);
        $this->Cell(80);
        $this->Cell(30, 10, utf8_decode('SR. PIE QUIROPEDIA'), 0, 1, 'C');
        $this->SetFont('Arial', '', 10);
        $this->Cell(80);
        $this->Cell(30, 5, utf8_decode('Recibo de Pago de Comisiones'), 0, 1, 'C');
        $this->Ln(15);
    }
}

$pdf = new PDF();
$pdf->AddPage();

$pdf->SetFont('Arial', 'I', 9);
$pdf->Cell(0, 5, utf8_decode('Tasa BCV hoy: ' . number_format($tasa_actual, 2, ',', '.') . ' Bs/$ '), 0, 1, 'R');
$pdf->Ln(2);

$pdf->SetFillColor(13, 110, 253);
$pdf->SetTextColor(255, 255, 255);
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(0, 8, utf8_decode(' DATOS DEL QUIROPEDISTA'), 0, 1, 'L', true);

$pdf->SetTextColor(0, 0, 0);
$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(25, 8, 'Nombre: ', 0, 0);
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(90, 8, utf8_decode($quiro['primer_nombre'] . ' ' . $quiro['primer_apellido']), 0, 0);
$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(25, 8, utf8_decode('Cédula: '), 0, 0);
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(0, 8, $quiro['tipo_doc'] . '-' . $quiro['cedula_id'], 0, 1);
$pdf->Ln(5);

$pdf->SetFont('Arial', 'B', 11);
$pdf->Cell(0, 8, 'Desglose Cronologico de Citas Atendidas:', 0, 1);

$pdf->SetFillColor(230, 230, 230);
$pdf->SetFont('Arial', 'B', 8); 
$pdf->Cell(20, 8, 'Fecha', 1, 0, 'C', true);
$pdf->Cell(35, 8, 'Paciente', 1, 0, 'C', true);
$pdf->Cell(55, 8, 'Servicio Aplicado', 1, 0, 'C', true);
$pdf->Cell(40, 8, 'Ingreso ($ y Bs)', 1, 0, 'C', true);
$pdf->Cell(40, 8, utf8_decode('Comisión ($ y Bs)'), 1, 1, 'C', true);

$pdf->SetFont('Arial', '', 8);
foreach ($servicios as $s) {
    $nombre_paciente = substr($s['primer_nombre'] . ' ' . $s['primer_apellido'], 0, 18);
    
    $ingreso_texto = '$' . number_format($s['subtotal_usd'], 2) . ' / ' . number_format($s['subtotal_bs'], 2, ',', '.') . ' Bs';
    $comision_texto = '$' . number_format($s['comision_usd'], 2) . ' / ' . number_format($s['comision_bs'], 2, ',', '.') . ' Bs';

    $x = $pdf->GetX();
    $y = $pdf->GetY();
    
    $pdf->Rect($x, $y, 20, 10);
    $pdf->SetXY($x, $y + 1);
    $pdf->Cell(20, 8, date('d/m/Y', strtotime($s['fecha_cita'])), 0, 0, 'C');
    
    $pdf->SetXY($x + 20, $y);
    $pdf->Rect($x + 20, $y, 35, 10);
    $pdf->SetXY($x + 20, $y + 1);
    $pdf->Cell(35, 8, utf8_decode($nombre_paciente), 0, 0, 'L');
    
    $pdf->SetXY($x + 55, $y);
    $pdf->Rect($x + 55, $y, 55, 10);
    $pdf->SetXY($x + 55, $y + 1);
    $pdf->MultiCell(55, 4, utf8_decode(substr($s['servicio_nombre'], 0, 30) . "\n(Tasa: " . number_format($s['tasa_del_dia'], 2, ',', '.') . " | " . floatval($s['comision_porcentaje']) . "%)"), 0, 'L');
    
    $pdf->SetXY($x + 110, $y);
    $pdf->Rect($x + 110, $y, 40, 10);
    $pdf->SetXY($x + 110, $y + 1);
    $pdf->Cell(40, 8, $ingreso_texto, 0, 0, 'C');
    
    $pdf->SetXY($x + 150, $y);
    $pdf->Rect($x + 150, $y, 40, 10);
    $pdf->SetXY($x + 150, $y + 1);
    $pdf->Cell(40, 8, $comision_texto, 0, 1, 'C');
    
    $pdf->SetY($y + 10);
}
$pdf->Ln(8);

$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(100, 10, 'Total Ingresos Generados:', 0, 0, 'R');
$texto_ventas = '$' . number_format($totales['total_ventas_usd'], 2) . '  =  ' . number_format($totales['total_ventas_bs'], 2, ',', '.') . ' Bs';
$pdf->Cell(90, 10, $texto_ventas, 0, 1, 'R');

$pdf->SetTextColor(13, 110, 253);

$pdf->Cell(100, 10, utf8_decode('TOTAL COMISIÓN A PAGAR:'), 0, 0, 'R');
$pdf->SetFont('Arial', 'B', 14);
$texto_comision = '$' . number_format($totales['total_comision_usd'], 2) . '  =  ' . number_format($totales['total_comision_bs'], 2, ',', '.') . ' Bs';
$pdf->Cell(90, 10, $texto_comision, 0, 1, 'R');

// Guardamos el PDF en memoria
$pdf_en_memoria = $pdf->Output('S'); 

// ========================================================
// PRIMERO ENVIAMOS EL CORREO
// ========================================================
$asunto = 'Recibo de Pago de Comisiones - Sr. Pie Quiropedia';
$cuerpo = 'Hola <b>' . $quiro['primer_nombre'] . '</b>,<br><br>Adjunto enviamos el detalle de los servicios que realizaste y el cálculo de tu pago de comisión correspondiente a la fecha.<br><br>¡Gracias por tu excelente labor en la clínica!<br><br><b>La Gerencia - Quiropedia Sr. Pie </b>';
$nombre_archivo = 'Recibo_Comisiones_' . $quiro['cedula_id'] . '.pdf';

// 1. Enviar al Quiropedista
$envio_quiro = enviarEmailConPDF(
    $quiro['correo'], 
    $quiro['primer_nombre'] . ' ' . $quiro['primer_apellido'], 
    $asunto, 
    $cuerpo, 
    $pdf_en_memoria, 
    $nombre_archivo
);

// ========================================================
// SEGUNDO: SOLO SI EL CORREO SE ENVIÓ, LIQUIDAMOS EN BD
// ========================================================
if ($envio_quiro) {
    
    if (isset($_GET['liquidar']) && $_GET['liquidar'] == '1') {
        $sql_liquidar = "UPDATE citas SET estado_comision = 1 
                         WHERE quiropedista_cedula = :cedula 
                         AND estado_comision = 0 
                         AND estatus = 'atendida'";
        $stmt_liquidar = $conexion->prepare($sql_liquidar);
        $stmt_liquidar->execute([':cedula' => $cedula]);
    }
    
    // Enviar copia a gerencia (opcional, si falla no rompe el proceso porque ya le llegó al quiro)
    $correo_clinica = 'srpiequiropedia4@gmail.com'; 
    $cuerpo_gerencia = 'Se ha generado y enviado el pago de comisiones para <b>' . $quiro['primer_nombre'] . ' ' . $quiro['primer_apellido'] . '</b>.<br>Adjunto el comprobante en PDF para los registros de la clínica.';
    enviarEmailConPDF($correo_clinica, 'Gerencia Sr. Pie', 'COPIA RESPALDO: ' . $asunto, $cuerpo_gerencia, $pdf_en_memoria, $nombre_archivo);

    echo "<script>alert('✅ Recibo enviado exitosamente. El contador se ha reiniciado.'); window.close();</script>";
} else {
    // Si el correo falló, NO se actualiza la BD, permitiendo volver a intentarlo
    echo "<script>alert('❌ Error al conectar con el servidor de correo. El pago NO fue procesado para que puedas intentarlo de nuevo.'); window.close();</script>";
}
?>
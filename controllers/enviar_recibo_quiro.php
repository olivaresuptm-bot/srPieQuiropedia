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

// Totales usando la Tasa Histórica y la Comisión Dinámica de cada servicio
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
");
$stmt_totales->execute([$cedula]);
$totales = $stmt_totales->fetch(PDO::FETCH_ASSOC);

// Desglose cronológico usando Tasa Histórica y Comisión Dinámica
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
    ORDER BY c.fecha ASC
");
$stmt_servicios->execute([$cedula]);
$servicios = $stmt_servicios->fetchAll(PDO::FETCH_ASSOC);

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

$pdf->SetTextColor(13, 110, 253); // Letra azul

$pdf->Cell(100, 10, utf8_decode('TOTAL COMISIÓN A PAGAR:'), 0, 0, 'R');
$pdf->SetFont('Arial', 'B', 14);
$texto_comision = '$' . number_format($totales['total_comision_usd'], 2) . '  =  ' . number_format($totales['total_comision_bs'], 2, ',', '.') . ' Bs';
$pdf->Cell(90, 10, $texto_comision, 0, 1, 'R');

$pdf_en_memoria = $pdf->Output('S'); 

// Configuración del correo
$asunto = 'Recibo de Pago de Comisiones - Sr. Pie Quiropedia';
$cuerpo = 'Hola <b>' . $quiro['primer_nombre'] . '</b>,<br><br>Adjunto enviamos el detalle de los servicios que realizaste y el cálculo de tu pago de comisión correspondiente a la fecha.<br><br>¡Gracias por tu excelente labor en la clínica!<br><br><b>La Gerencia - Sr. Pie Quiropedia</b>';
$nombre_archivo = 'Recibo_Comisiones_' . $quiro['cedula_id'] . '.pdf';

$envio_exitoso = enviarEmailConPDF(
    $quiro['correo'], 
    $quiro['primer_nombre'] . ' ' . $quiro['primer_apellido'], 
    $asunto, 
    $cuerpo, 
    $pdf_en_memoria, 
    $nombre_archivo
);

if ($envio_exitoso) {
    echo "<script>alert('✅ Recibo enviado exitosamente al correo del especialista.'); window.close();</script>";
} else {
    echo "<script>alert('❌ Error al enviar el correo. Verifica tu configuración o conexión.'); window.close();</script>";
}
?>
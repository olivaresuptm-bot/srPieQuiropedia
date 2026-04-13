<?php
session_start();
date_default_timezone_set('America/Caracas');
define('FPDF_FONTPATH', '../includes/fpdf/font/');
require_once '../includes/fpdf/fpdf.php';
require_once '../includes/db.php';

// Validar que se reciba la cédula
$cedula = $_GET['cedula'] ?? '';
if (!$cedula) die("Error: Cédula de paciente no proporcionada.");

// Buscar los datos del paciente en la base de datos
$stmt = $conexion->prepare("SELECT * FROM pacientes WHERE cedula_id = ?");
$stmt->execute([$cedula]);
$paciente = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$paciente) die("Paciente no encontrado.");

// Clase para crear el Membrete y Pie de página
class PDF extends FPDF {
    function Header() {
        // Logo de la clínica
        $this->Image('../assets/img/logo_sr_pie.png', 15, 10, 25);
        $this->SetFont('Arial', 'B', 16);
        $this->Cell(80);
        $this->Cell(30, 10, utf8_decode('CLÍNICA QUIROPEDIA SR. PIE'), 0, 1, 'C');
        $this->SetFont('Arial', '', 11);
        $this->Cell(80);
        $this->Cell(30, 5, utf8_decode('Especialistas en el cuidado integral del pie'), 0, 1, 'C');
        $this->SetFont('Arial', 'B', 12);
        $this->SetFont('Arial', '', 8);
        $this->Cell(80);
        $this->Cell(30, 5, utf8_decode('RIF: J-41230047-4'), 0, 1, 'C');
        $this->Cell(80);
        $this->Cell(30, 5, utf8_decode('Tel: (0274) 266-6818 / (0414) 735-9726'), 0, 1, 'C');
        $this->Cell(80);
        $this->Cell(30, 5, utf8_decode('Mérida, Venezuela'), 0, 1, 'C');
        $this->Cell(80);
        $this->Cell(30, 5, utf8_decode('C.C. Las Tapias Nivel 1 Local 57'), 0, 1, 'C');
        $this->Cell(80);
        $this->Cell(30, 5, utf8_decode('quiropediasrpie@gmail.com'), 0, 1, 'C');
        // Línea separadora
        $this->SetDrawColor(13, 110, 253); // Color Azul
        $this->SetLineWidth(0.5);
        $this->Line(15, 50, 195, 50);
        $this->Ln(20);
    }

    function Footer() {
        $this->SetY(-20);
        $this->SetFont('Arial', 'I', 8);
        $this->SetTextColor(128, 128, 128);
        $this->Cell(0, 10, utf8_decode('Documento generado por la clínica Quiropedia Sr. Pie - ' . date('d/m/Y h:i A')), 0, 0, 'C');
    }
}

$pdf = new PDF();
$pdf->AddPage();
$pdf->SetTextColor(0, 0, 0);

// Título del documento
$pdf->SetFont('Arial', 'B', 16);
$pdf->Cell(0, 10, utf8_decode('JUSTIFICATIVO MÉDICO'), 0, 1, 'C');
$pdf->Ln(5);

// Crear la fecha en formato texto (Ej: 10 de Abril de 2026)
$meses = ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'];
$fecha_actual = date('d') . ' de ' . $meses[date('n') - 1] . ' de ' . date('Y');

$pdf->SetFont('Arial', '', 12);
$pdf->Cell(0, 10, utf8_decode('Mérida, ' . $fecha_actual), 0, 1, 'R');
$pdf->Ln(10);

// Cuerpo principal del justificativo
$nombre_completo = $paciente['primer_nombre'] . ' ' . $paciente['segundo_nombre'] . ' ' . $paciente['primer_apellido'] . ' ' . $paciente['segundo_apellido'];
$cedula_formato = $paciente['tipo_doc'] . '-' . $paciente['cedula_id'];

$cuerpo = "Por medio de la presente comunicación, se hace constar que el (la) paciente " . trim($nombre_completo) . ", titular de la Cédula de Identidad Nro. " . $cedula_formato . ", acudió el día de hoy a nuestras instalaciones para recibir atención, evaluación y o tratamiento quiropédico especializado.\n\n" .
          "Se expide la presente constancia a petición de la parte interesada para los fines legales, laborales o académicos que considere pertinentes.";

$pdf->SetFont('Arial', '', 12);
// Interlineado agradable
$pdf->MultiCell(0, 8, utf8_decode($cuerpo), 0, 'J');
$pdf->Ln(15);

// Sección de Observaciones a mano
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(0, 8, utf8_decode('Observaciones / Días de Reposo (Si aplica):'), 0, 1, 'L');
$pdf->SetDrawColor(0, 0, 0);
$pdf->SetLineWidth(0.2);

// Líneas para escribir
$x_start = $pdf->GetX();
$y_start = $pdf->GetY();
$pdf->Line($x_start, $y_start + 8, $x_start + 190, $y_start + 8);
$pdf->Line($x_start, $y_start + 18, $x_start + 190, $y_start + 18);
$pdf->Line($x_start, $y_start + 28, $x_start + 190, $y_start + 28);

$pdf->Ln(60); 

// ==========================================
// SECCIÓN DE FIRMA Y SELLO
// ==========================================
$pdf->SetFont('Arial', 'B', 11);
$y = $pdf->GetY();

// 1. Línea para la firma (Izquierda)
$pdf->Line(30, $y, 90, $y);
$pdf->SetXY(30, $y + 2);
$pdf->Cell(60, 5, 'Firma del Especialista', 0, 0, 'C');

// 2. Cuadro para el Sello (Derecha)
// Dibujamos un rectángulo suave para indicar dónde va el sello
$pdf->SetDrawColor(200, 200, 200); 
$pdf->Rect(125, $y - 25, 45, 35);
$pdf->SetXY(125, $y + 12);
$pdf->SetTextColor(150, 150, 150);
$pdf->SetFont('Arial', 'I', 9);
$pdf->Cell(45, 5, 'Sello de la Clinica', 0, 0, 'C');

// Generar PDF y abrirlo en una nueva pestaña
$pdf->Output('I', 'Justificativo_' . $paciente['cedula_id'] . '.pdf');
?>
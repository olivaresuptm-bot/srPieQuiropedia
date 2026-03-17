<?php
session_start();
require_once '../includes/db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validar que se enviaron los datos
    if (!isset($_POST['cita_id']) || empty($_POST['metodo_pago'])) {
        die("Error: Datos incompletos.");
    }

    $cita_id = $_POST['cita_id'];
    $monto = $_POST['monto'];
    $metodo_pago = $_POST['metodo_pago'];
    $referencia = !empty($_POST['referencia']) ? trim($_POST['referencia']) : null;
    $fecha_actual = date('Y-m-d H:i:s');

    try {
        // Verificar si la cita ya fue pagada para evitar cobros dobles
        $check = $conexion->prepare("SELECT pago_id FROM pagos WHERE cita_id = ?");
        $check->execute([$cita_id]);
        
        if ($check->rowCount() > 0) {
            echo "<script>
                    alert('⚠️ Esta cita ya tiene un pago registrado.');
                    window.location.href = '../modulos/actualizar_citas.php';
                  </script>";
            exit;
        }

        // Insertar en la tabla pagos
        $sql = "INSERT INTO pagos (cita_id, monto, metodo_pago, fecha_pago, referencia) 
                VALUES (:cita, :monto, :metodo, :fecha, :referencia)";
        
        $stmt = $conexion->prepare($sql);
        $stmt->execute([
            ':cita' => $cita_id,
            ':monto' => $monto,
            ':metodo' => $metodo_pago,
            ':fecha' => $fecha_actual,
            ':referencia' => $referencia
        ]);

        // Redirigir con éxito
        echo "<script>
                alert('✅ Pago registrado correctamente.');
                window.location.href = '../modulos/actualizar_citas.php';
              </script>";

    } catch(PDOException $e) {
        // Manejar errores de BD
        echo "<script>
                alert('❌ Error al procesar el pago: " . addslashes($e->getMessage()) . "');
                window.history.back();
              </script>";
    }
} else {
    header("Location: ../modulos/actualizar_citas.php");
    exit;
}
?>
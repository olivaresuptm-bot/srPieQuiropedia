<?php
require_once __DIR__ . '/../includes/db.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accion'])) {
    $accion = $_POST['accion'];

    // CREAR / EDITAR SERVICIO
    if ($accion === 'crear' || $accion === 'editar') {
        $nombre = trim($_POST['nombre']);
        $descripcion = trim($_POST['descripcion']);
        $precio = $_POST['precio'];
        
        // Capturamos el porcentaje de comisión (si viene vacío, le ponemos 40 por defecto)
        $comision = $_POST['comision_porcentaje'] ?? 40; 
        
        $id = $_POST['id'] ?? null;

        try {
            if ($accion === 'crear') {
                $sql = "INSERT INTO servicios (nombre, descripcion, precio, comision_porcentaje, estatus) VALUES (?, ?, ?, ?, 1)";
                $stmt = $conexion->prepare($sql);
                $stmt->execute([$nombre, $descripcion, $precio, $comision]);
                $msg = "Servicio creado con éxito";
            } else {
                $sql = "UPDATE servicios SET nombre = ?, descripcion = ?, precio = ?, comision_porcentaje = ? WHERE servicio_id = ?";
                $stmt = $conexion->prepare($sql);
                $stmt->execute([$nombre, $descripcion, $precio, $comision, $id]);
                $msg = "Servicio actualizado correctamente";
            }
            echo "<script>alert('✅ $msg'); window.location.href='../modulos/servicios.php';</script>";
        } catch (PDOException $e) {
            echo "<script>alert('❌ Error: " . addslashes($e->getMessage()) . "'); window.history.back();</script>";
        }
    }

    // ACTIVAR / DESACTIVAR SERVICIO
    if ($accion === 'alternar_estado') {
        $id = $_POST['id'];
        $nuevo_estatus = $_POST['nuevo_estatus'];
        try {
            $stmt = $conexion->prepare("UPDATE servicios SET estatus = ? WHERE servicio_id = ?");
            $stmt->execute([$nuevo_estatus, $id]);
            header("Location: ../modulos/servicios.php");
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
    }
}
?>
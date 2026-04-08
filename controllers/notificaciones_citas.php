<?php
// =====================================================
// SISTEMA DE NOTIFICACIÓN DE CITAS (ejecución en segundo plano)
// =====================================================

$fecha_hoy = date('Y-m-d');
$carpeta_logs = __DIR__ . '/../logs';
$archivo_lock = $carpeta_logs . '/candado_diario.txt';
$archivo_log = $carpeta_logs . '/notificaciones.log';

// Crear directorio de logs si no existe
if (!file_exists($carpeta_logs)) {
    mkdir($carpeta_logs, 0777, true);
}

// 1. EL CANDADO DIARIO (A PRUEBA DE FALLOS)
// Solo lee un archivo de texto simple con la fecha. Si es hoy, bloquea la ejecución.
$ya_se_ejecuto = false;
if (file_exists($archivo_lock)) {
    $ultima_fecha = trim(file_get_contents($archivo_lock));
    if ($ultima_fecha === $fecha_hoy) {
        $ya_se_ejecuto = true;
    }
}

// Solo ejecutar si no se ha notificado hoy
if (!$ya_se_ejecuto) {
    
    // Bloqueamos inmediatamente creando/sobreescribiendo el archivo con la fecha de hoy
    // Así evitamos correos dobles si alguien abre dos pestañas al mismo tiempo
    file_put_contents($archivo_lock, $fecha_hoy);
    
    // Función para enviar correo
    function enviarNotificacion($correo_destino, $nombre_destino, $tipo_notificacion, $citas_info) {
        require_once __DIR__ . '/../includes/mailer.php';
        
        $asunto = "Recordatorio de Cita - Sr. Pie Quiropedia";
        $es_clinica = ($nombre_destino === 'Administración'); 
        
        if ($tipo_notificacion == '24h') {
            $color = "#e67e22";
            $mensaje_adicional = $es_clinica ? "Citas programadas para MAÑANA:" : "Tu cita es MAÑANA. Por favor confirma tu asistencia.";
        } else {
            $color = "#3498db";
            $mensaje_adicional = $es_clinica ? "Citas programadas en los próximos 3 días:" : "Tienes una cita programada en los próximos 3 días.";
        }
        
        $header_paciente = $es_clinica ? "<th style='background: #f5f5f5; padding: 10px; border: 1px solid #ddd; text-align: left;'>Paciente</th>" : "";
        
        $tabla_citas = "";
        foreach ($citas_info as $cita) {
            $col_paciente = $es_clinica ? "<td style='padding: 8px; border: 1px solid #ddd;'>" . htmlspecialchars($cita['paciente'] ?? 'N/A') . "</td>" : "";
            
            $tabla_citas .= "
                <tr>
                    <td style='padding: 8px; border: 1px solid #ddd;'>" . date('d/m/Y', strtotime($cita['fecha'])) . "</td>
                    <td style='padding: 8px; border: 1px solid #ddd;'>" . date('h:i A', strtotime($cita['hora'])) . "</td>
                    $col_paciente
                    <td style='padding: 8px; border: 1px solid #ddd;'>" . htmlspecialchars($cita['servicio']) . "</td>
                </tr>
            ";
        }
        
        $cuerpo = "
        <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; border: 1px solid #e0e0e0; border-radius: 10px; overflow: hidden;'>
            <div style='background: $color; padding: 20px; text-align: center; color: white;'>
                <h2 style='margin: 0;'>Sr. Pie Quiropedia</h2>
                <p style='margin: 5px 0 0; opacity: 0.9;'>Recordatorio de Citas</p>
            </div>
            <div style='padding: 20px;'>
                <p>Hola <strong>" . htmlspecialchars($nombre_destino) . "</strong>,</p>
                <p>$mensaje_adicional</p>
                <table style='width: 100%; border-collapse: collapse; margin: 15px 0;'>
                    <thead>
                        <tr>
                            <th style='background: #f5f5f5; padding: 10px; border: 1px solid #ddd; text-align: left;'>Fecha</th>
                            <th style='background: #f5f5f5; padding: 10px; border: 1px solid #ddd; text-align: left;'>Hora</th>
                            $header_paciente
                            <th style='background: #f5f5f5; padding: 10px; border: 1px solid #ddd; text-align: left;'>Servicio</th>
                        </tr>
                    </thead>
                    <tbody>
                        $tabla_citas
                    </tbody>
                </table>
                <hr style='margin: 20px 0; border: none; border-top: 1px solid #eee;'>
                <p style='color: #666; font-size: 12px; text-align: center;'>
                    Este es un mensaje automático generado por el sistema Sr. Pie Quiropedia.
                </p>
            </div>
        </div>";
        
        return enviarEmail($correo_destino, $asunto, $cuerpo);
    }
    
    try {
        if (!isset($conexion) || $conexion === null) {
            throw new Exception("No hay conexión a la base de datos");
        }
        
        // 1. Obtener citas EXACTAMENTE para mañana (1 día de distancia)
        $sql_24h = "SELECT c.cita_id, c.fecha, c.hora, c.estatus, c.aviso,
                           p.primer_nombre, p.primer_apellido, p.correo as correo_paciente,
                           s.nombre as servicio
                    FROM citas c
                    JOIN pacientes p ON c.paciente_cedula = p.cedula_id
                    JOIN servicios s ON c.servicio_id = s.servicio_id
                    WHERE c.fecha = DATE_ADD(CURDATE(), INTERVAL 1 DAY)
                    AND c.estatus = 'programada'
                    AND c.aviso != 'S'
                    AND p.correo IS NOT NULL AND p.correo != ''";
        
        $stmt_24h = $conexion->query($sql_24h);
        $citas_24h = $stmt_24h->fetchAll(PDO::FETCH_ASSOC);
        
        // 2. Obtener citas EXACTAMENTE para dentro de 3 días (72h)
        $sql_72h = "SELECT c.cita_id, c.fecha, c.hora, c.estatus, c.aviso,
                           p.primer_nombre, p.primer_apellido, p.correo as correo_paciente,
                           s.nombre as servicio
                    FROM citas c
                    JOIN pacientes p ON c.paciente_cedula = p.cedula_id
                    JOIN servicios s ON c.servicio_id = s.servicio_id
                    WHERE c.fecha = DATE_ADD(CURDATE(), INTERVAL 3 DAY)
                    AND c.estatus = 'programada'
                    AND p.correo IS NOT NULL AND p.correo != ''";
        
        $stmt_72h = $conexion->query($sql_72h);
        $citas_72h = $stmt_72h->fetchAll(PDO::FETCH_ASSOC);
        
        // 3. Correo de la clínica
        $correo_clinica = "srpiequiropedia4@gmail.com"; 
        
        // 4. Procesar notificaciones de 24 horas
        if (!empty($citas_24h)) {
            $citas_por_paciente_24h = [];
            foreach ($citas_24h as $cita) {
                $key = $cita['correo_paciente'];
                if (!isset($citas_por_paciente_24h[$key])) {
                    $citas_por_paciente_24h[$key] = [
                        'nombre' => $cita['primer_nombre'] . ' ' . $cita['primer_apellido'],
                        'correo' => $cita['correo_paciente'],
                        'citas' => []
                    ];
                }
                $citas_por_paciente_24h[$key]['citas'][] = $cita;
            }
            
            foreach ($citas_por_paciente_24h as $paciente) {
                enviarNotificacion($paciente['correo'], $paciente['nombre'], '24h', $paciente['citas']);
                // Marcar en la BD que el aviso de las 24h ya se envió
                foreach ($paciente['citas'] as $cita) {
                    $update = $conexion->prepare("UPDATE citas SET aviso = 'S' WHERE cita_id = ?");
                    $update->execute([$cita['cita_id']]);
                }
            }
            
            $resumen_clinica = [];
            foreach ($citas_24h as $cita) {
                $resumen_clinica[] = [
                    'fecha' => $cita['fecha'],
                    'hora' => $cita['hora'],
                    'paciente' => $cita['primer_nombre'] . ' ' . $cita['primer_apellido'],
                    'servicio' => $cita['servicio']
                ];
            }
            enviarNotificacion($correo_clinica, "Administración", '24h', $resumen_clinica);
        }
        
        // 5. Procesar notificaciones de 72 horas
        if (!empty($citas_72h)) {
            $citas_por_paciente_72h = [];
            foreach ($citas_72h as $cita) {
                $key = $cita['correo_paciente'];
                if (!isset($citas_por_paciente_72h[$key])) {
                    $citas_por_paciente_72h[$key] = [
                        'nombre' => $cita['primer_nombre'] . ' ' . $cita['primer_apellido'],
                        'correo' => $cita['correo_paciente'],
                        'citas' => []
                    ];
                }
                $citas_por_paciente_72h[$key]['citas'][] = $cita;
            }
            
            foreach ($citas_por_paciente_72h as $paciente) {
                enviarNotificacion($paciente['correo'], $paciente['nombre'], '72h', $paciente['citas']);
            }
            
            $resumen_clinica_72h = [];
            foreach ($citas_72h as $cita) {
                $resumen_clinica_72h[] = [
                    'fecha' => $cita['fecha'],
                    'hora' => $cita['hora'],
                    'paciente' => $cita['primer_nombre'] . ' ' . $cita['primer_apellido'],
                    'servicio' => $cita['servicio']
                ];
            }
            enviarNotificacion($correo_clinica, "Administración", '72h', $resumen_clinica_72h);
        }
        
        $log_entry = date('Y-m-d H:i:s') . " - Notificaciones enviadas: " . 
                     (count($citas_24h) + count($citas_72h)) . " citas procesadas\n";
        file_put_contents($archivo_log, $log_entry, FILE_APPEND);
        
    } catch (Exception $e) {
        // Si hay un error, borramos el candado para que el sistema intente de nuevo en el próximo inicio de sesión
        if (file_exists($archivo_lock)) unlink($archivo_lock);
        
        error_log("Error en sistema de notificaciones: " . $e->getMessage());
        $error_log_entry = date('Y-m-d H:i:s') . " - ERROR: " . $e->getMessage() . "\n";
        file_put_contents($archivo_log, $error_log_entry, FILE_APPEND);
    }
}
?>
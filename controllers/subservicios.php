<?php
    $stmt = $conexion->query("SELECT * FROM servicios ORDER BY precio DESC, nombre ASC");
    while ($s = $stmt->fetch(PDO::FETCH_ASSOC)):
        $precio_bs = $s['precio'] * $tasa_bcv;
        $es_control = ($s['precio'] <= 0); 
        
  
        $texto_precio = '$' . number_format($s['precio'], 2);
        $etiqueta_insignia = '';
        
        if ($es_control) {
           
            $nombre_lower = strtolower(trim($s['nombre']));
            
         
            if (strpos($nombre_lower, 'revision') !== false || strpos($nombre_lower, 'revisión') !== false) {
                $texto_precio = 'Garantía';
                $etiqueta_insignia = 'GARANTÍA';
            } 
    
            elseif (strpos($nombre_lower, 'cura') !== false) {
                $texto_precio = 'Cura incluida';
                $etiqueta_insignia = 'CURA INCLUIDA';
            } 
           
            else {
                $texto_precio = 'Gratis';
                $etiqueta_insignia = 'CONTROL / SEGUIMIENTO';
            }
        }
    ?>
    <tr class="<?php echo $es_control ? 'table-light' : ''; ?>">
        <td class="ps-4">
            <strong><?php echo htmlspecialchars($s['nombre']); ?></strong>
            <?php if($es_control): ?>
                <br><span class="badge bg-info text-dark" style="font-size: 0.7rem;"><?php echo $etiqueta_insignia; ?></span>
            <?php endif; ?>
        </td>
        <td class="small text-muted"><?php echo htmlspecialchars($s['descripcion']); ?></td>
        <td class="fw-bold <?php echo $es_control ? 'text-primary' : 'text-success'; ?>">
            <?php echo $texto_precio; ?>
        </td>
        <td class="text-secondary">
            <?php echo $es_control ? '-' : 'Bs. ' . number_format($precio_bs, 2, ',', '.'); ?>
        </td>
        
        <td class="fw-bold text-info">
            <?php echo $es_control ? '-' : floatval($s['comision_porcentaje'] ?? 40) . '%'; ?>
        </td>

        <td>
            <span class="badge <?php echo $s['estatus'] ? 'bg-success' : 'bg-danger'; ?>">
                <?php echo $s['estatus'] ? 'Activo' : 'Inactivo'; ?>
            </span>
        </td>
        <td class="text-center">
            <button class="btn btn-sm btn-warning shadow-sm" onclick="abrirEditar(<?php echo htmlspecialchars(json_encode($s), ENT_QUOTES, 'UTF-8'); ?>)">
                <i class="bi bi-pencil"></i>
            </button>
            <form action="../controllers/servicios_controller.php" method="POST" class="d-inline" onsubmit="return confirm('¿Cambiar estado?');">
                <input type="hidden" name="id" value="<?php echo $s['servicio_id']; ?>">
                <input type="hidden" name="nuevo_estatus" value="<?php echo $s['estatus'] ? 0 : 1; ?>">
                <button type="submit" name="accion" value="alternar_estado" class="btn btn-sm btn-outline-secondary shadow-sm">
                    <i class="bi bi-arrow-repeat"></i>
                </button>
            </form>
        </td>
    </tr>
<?php endwhile; ?>
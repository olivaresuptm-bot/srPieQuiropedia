 <?php
    $stmt = $conexion->query("SELECT * FROM servicios ORDER BY precio DESC, nombre ASC");
    while ($s = $stmt->fetch(PDO::FETCH_ASSOC)):
        $precio_bs = $s['precio'] * $tasa_bcv;
        $es_control = ($s['precio'] <= 0); // Verificamos si es gratis
    ?>
    <tr class="<?php echo $es_control ? 'table-light' : ''; ?>">
        <td class="ps-4">
            <strong><?php echo htmlspecialchars($s['nombre']); ?></strong>
            <?php if($es_control): ?>
                <br><span class="badge bg-info text-dark" style="font-size: 0.7rem;">CONTROL / SEGUIMIENTO</span>
            <?php endif; ?>
        </td>
        <td class="small text-muted"><?php echo htmlspecialchars($s['descripcion']); ?></td>
        <td class="fw-bold <?php echo $es_control ? 'text-muted' : 'text-success'; ?>">
            <?php echo $es_control ? 'GRATIS' : '$' . number_format($s['precio'], 2); ?>
        </td>
        <td class="text-secondary">
            <?php echo $es_control ? '-' : 'Bs. ' . number_format($precio_bs, 2, ',', '.'); ?>
        </td>
        <td>
            <span class="badge <?php echo $s['estatus'] ? 'bg-success' : 'bg-danger'; ?>">
                <?php echo $s['estatus'] ? 'Activo' : 'Inactivo'; ?>
            </span>
        </td>
        <td class="text-center">
            <button class="btn btn-sm btn-warning" onclick="abrirEditar(<?php echo htmlspecialchars(json_encode($s)); ?>)">
                <i class="bi bi-pencil"></i>
            </button>
            <form action="../controllers/servicios_controller.php" method="POST" class="d-inline" onsubmit="return confirm('¿Cambiar estado?');">
                <input type="hidden" name="id" value="<?php echo $s['servicio_id']; ?>">
                <input type="hidden" name="nuevo_estatus" value="<?php echo $s['estatus'] ? 0 : 1; ?>">
                <button type="submit" name="accion" value="alternar_estado" class="btn btn-sm btn-outline-secondary">
                    <i class="bi bi-arrow-repeat"></i>
                </button>
            </form>
        </td>
    </tr>
    <?php endwhile; ?>
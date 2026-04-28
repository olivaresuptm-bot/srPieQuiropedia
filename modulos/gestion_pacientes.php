<?php
session_start();
if (!isset($_SESSION['usuario_id'])) {
    header("Location: ../index.php");
    exit;
}

require_once '../includes/db.php';

// Configuración de la paginación
$por_pagina = 10;
$pagina = isset($_GET['p']) ? (int)$_GET['p'] : 1;
if ($pagina <= 0) $pagina = 1;
$inicio = ($pagina - 1) * $por_pagina;

try {
    // 1. Contamos el total de pacientes para calcular las páginas
    $stmt_total = $conexion->query("SELECT COUNT(*) FROM pacientes");
    $total_pacientes = $stmt_total->fetchColumn();
    $total_paginas = ceil($total_pacientes / $por_pagina);

    // 2. Traemos solo los 10 pacientes de la página actual
    $stmt_pacientes = $conexion->query("SELECT cedula_id, primer_nombre, primer_apellido FROM pacientes ORDER BY primer_nombre ASC LIMIT $inicio, $por_pagina");
    $pacientes = $stmt_pacientes->fetchAll(PDO::FETCH_ASSOC);

} catch(PDOException $e) {
    $pacientes = [];
    $total_pacientes = 0;
    $total_paginas = 1;
}

    if (isset($_GET['ajax_filtro'])) {
        $query = $_GET['ajax_filtro'] . '%';
        $stmt = $conexion->prepare("SELECT cedula_id, primer_nombre, primer_apellido FROM pacientes WHERE cedula_id LIKE ? ORDER BY primer_nombre ASC LIMIT 10");
        $stmt->execute([$query]);
        $pacientes_ajax = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (empty($pacientes_ajax)) {
            echo '<tr><td colspan="3" class="text-center text-muted py-4">No hay coincidencias.</td></tr>';
        } else {
            foreach ($pacientes_ajax as $p) {
                echo '<tr class="fila-paciente">
                        <td class="fw-bold col-cedula">'.htmlspecialchars($p['cedula_id']).'</td>
                        <td>'.htmlspecialchars($p['primer_nombre'] . " " . $p['primer_apellido']).'</td>
                        <td class="text-end">
                            <button class="btn btn-sm btn-outline-primary" onclick="document.getElementById(\'inputBusqueda\').value = \''.$p['cedula_id'].'\'; realizarBusqueda();">
                                <i class="bi bi-eye"></i>
                            </button>
                        </td>
                      </tr>';
            }
        }
        exit;
    }
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pacientes - Sr. Pie</title>
    <link rel="icon" type="image/png" href="../assets/img/logo_sr_pie.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../assets/css/gestion_pacientes.css">
    <link rel="stylesheet" href="../assets/css/sidebar.css">
    <link rel="stylesheet" href="../assets/css/header.css">
    <link rel="stylesheet" href="../assets/css/footer.css">
</head>

<body class="bg-light m-0 p-0 d-flex flex-column" style="height: 100vh; overflow: hidden;">
    <h1>Holaaa </h1>
    <p>Soy sofia</p>
   
    <?php include '../includes/header.php'; ?>

    <div class="d-flex flex-grow-1" style="min-height: 0; overflow: hidden;">
        <?php include '../includes/sidebar.php'; 
        include '../includes/titulo_modulo.php';?>
        
        <div class="flex-grow-1 p-4" style="overflow-y: auto; background-color: #f8f9fa;">
        
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-body p-4">
                    <div class="row justify-content-center">
                        <div class="col-md-10 col-lg-8">
                            <div class="input-group input-group-lg shadow-sm mb-3">
                                <span class="input-group-text bg-white border-end-0"><i class="bi bi-person-vcard text-primary"></i></span>
                                <input type="text" id="inputBusqueda" class="form-control border-start-0" 
                                    placeholder="Ingrese cédula para filtrar..." 
                                    oninput="this.value = this.value.replace(/[^0-9]/g, '')"
                                    maxlength="10"
                                    onkeyup="filtrarPacientes(event)">
                                <button class="btn btn-primary px-3 px-md-4" id="btnBuscar" onclick="realizarBusqueda()">Buscar</button>
                            </div>

                            <div id="resultadoFicha" class="mt-4 pt-4 border-top" style="display:none;"></div>
                            
                            <div id="errorBusqueda" class="text-danger mt-2" style="display:none;">
                                <i class="bi bi-exclamation-circle"></i> No se encontró el paciente.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
             <div id="panelOcultable">

             <div class="row justify-content-center">
                    <div class="col-md-10 col-lg-8">
                        <div class="card border-0 shadow-sm ">
                            <div class="card-body d-flex flex-column flex-md-row align-items-center justify-content-between text-center text-md-start gap-3">
                                <div class="d-flex flex-column flex-md-row align-items-center gap-3">
                                    <div class="icon-container-registro m-0" style="background: rgba(13,110,253,0.1); padding:20px; border-radius:50%;">
                                        <i class="bi bi-person-plus-fill text-primary" style="font-size: 2.5rem;"></i>
                                    </div>
                                    <div>
                                        <h4 class="fw-bold mb-1">¿Paciente nuevo?</h4>
                                        <p class="mb-0 text-muted">Registra un nuevo paciente en el sistema.</p>
                                    </div>
                                </div>
                                <a href="gestion_pacientes/pacientes.php" class="btn btn-primary btn-lg px-4 mt-2 mt-md-0 w-50 w-md-auto">Registrar paciente</a>
                            </div>
                        </div>
                    </div>

                <div class="row justify-content-center mt-4 px-2 px-md-0">
                    <div class="col-12 col-md-10 col-lg-8 text-center">
                        <span class="badge bg-white text-secondary border px-3 px-md-4 py-3 fs-6 shadow-sm w-100 text-wrap" style="border-radius: 15px;">
                            <i class="bi bi-people-fill text-primary me-2"></i>
                            Total de pacientes en el sistema: <strong class="text-dark fs-5"><?php echo number_format($total_pacientes); ?></strong>
                        </span>
                    </div>
                </div>

           
                
                <div class="row justify-content-center mb-4">
                    <div class="col-md-10 col-lg-8">
                        <div class="card border-0 shadow-sm">
                            <div class="table-responsive" style="max-height: 350px;">
                                <table class="table table-hover align-middle mb-0">
                                    <thead class="table-light sticky-top">
                                        <tr>
                                            <th>Cédula</th>
                                            <th>Nombre Completo</th>
                                            <th class="text-end">Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody id="tablaCuerpo">
                                        <?php if (empty($pacientes)): ?>
                                            <tr>
                                                <td colspan="3" class="text-center text-muted py-4">No hay pacientes registrados.</td>
                                            </tr>
                                        <?php else: ?>
                                            <?php foreach ($pacientes as $p): ?>
                                            <tr class="fila-paciente">
                                                <td class="fw-bold col-cedula"><?php echo htmlspecialchars($p['cedula_id']); ?></td>
                                                <td><?php echo htmlspecialchars($p['primer_nombre'] . " " . $p['primer_apellido']); ?></td>
                                                <td class="text-end">
                                                    <button class="btn btn-sm btn-outline-primary" 
                                                            onclick="document.getElementById('inputBusqueda').value = '<?php echo $p['cedula_id']; ?>'; realizarBusqueda();">
                                                        <i class="bi bi-eye"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                            
                            <?php if ($total_paginas > 1): ?>
                            <div class="card-footer bg-white border-0 py-3">
                                <nav>
                                    <ul class="pagination pagination-sm justify-content-end mb-0 shadow-sm">
                                        <li class="page-item <?php echo $pagina <= 1 ? 'disabled' : ''; ?>">
                                            <a class="page-link" href="?p=1"><<</a>
                                        </li>
                                        <li class="page-item <?php echo $pagina <= 1 ? 'disabled' : ''; ?>">
                                            <a class="page-link" href="?p=<?php echo $pagina - 1; ?>"><</a>
                                        </li>
                                        <li class="page-item active">
                                            <span class="page-link"><?php echo $pagina; ?> / <?php echo $total_paginas; ?></span>
                                        </li>
                                        <li class="page-item <?php echo $pagina >= $total_paginas ? 'disabled' : ''; ?>">
                                            <a class="page-link" href="?p=<?php echo $pagina + 1; ?>">></a>
                                        </li>
                                        <li class="page-item <?php echo $pagina >= $total_paginas ? 'disabled' : ''; ?>">
                                            <a class="page-link" href="?p=<?php echo $total_paginas; ?>">>></a>
                                        </li>
                                    </ul>
                                </nav>
                            </div>
                            <?php endif; ?>

                        </div>
                    </div>
                </div>

                
                </div>

                

            </div>
            </div> 
    </div>
    </div>
    </div>

    <?php include '../includes/footer.php'; ?>

    <script>
let timeoutBusqueda = null;

function filtrarPacientes(event) {
    let input = document.getElementById("inputBusqueda").value.trim();
    let tablaCuerpo = document.getElementById("tablaCuerpo");
    let paginacion = document.querySelector('.card-footer');

    // Manejo de Enter para búsqueda completa (ficha)
    if (event && event.key === 'Enter') {
        event.preventDefault();
        if (typeof realizarBusqueda === 'function') realizarBusqueda();
        return;
    }

    clearTimeout(timeoutBusqueda);

    if (input === "") {
        location.reload(); 
        return;
    }

    // Esperar 250ms antes de consultar a la BD
    timeoutBusqueda = setTimeout(() => {
        fetch(`gestion_pacientes.php?ajax_filtro=${input}`)
            .then(response => response.text())
            .then(html => {
                tablaCuerpo.innerHTML = html;
                // Aqui se oculta la paginación mientras se filtra para evitar confusión
                if (paginacion) paginacion.style.display = 'none';
            })
            .catch(error => console.error('Error en filtro dinámico:', error));
    }, 250);
}
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../assets/js/editar_paciente.js"></script>
    <script src="../assets/js/busqueda_paciente.js"></script>
    <script src="../assets/js/hamburguesa.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const resultadoFicha = document.getElementById('resultadoFicha');
            const panelOcultable = document.getElementById('panelOcultable');
            const inputBusqueda = document.getElementById('inputBusqueda');

            // Vigila si la ficha de datos de paciente pasa a ser visible
            const observer = new MutationObserver(function(mutations) {
                mutations.forEach(function(mutation) {
                    if (mutation.attributeName === 'style') {
                        if (resultadoFicha.style.display !== 'none') {
                            // Se encontró al paciente, oculta el resto de cosas
                            panelOcultable.style.display = 'none';
                        } else {
                            // No hay ficha, muestra la lista y el registro
                            panelOcultable.style.display = 'block';
                        }
                    }
                });
            });

            if (resultadoFicha) {
                observer.observe(resultadoFicha, { attributes: true });
            }

            // Si el usuario borra lo que escribió en el buscador, limpia la ficha y regresa la vista inicial
            if (inputBusqueda) {
                inputBusqueda.addEventListener('input', function() {
                    if(this.value.trim() === '') {
                        resultadoFicha.style.display = 'none';
                        resultadoFicha.innerHTML = '';
                        panelOcultable.style.display = 'block';
                        filtrarPacientes(); // Restaura la tabla completa
                    }
                });
            }
        });
    </script>

    <script>
    // Esto detecta si hay una cédula en la URL y dispara la búsqueda automáticamente
    window.addEventListener('DOMContentLoaded', (event) => {
        const urlParams = new URLSearchParams(window.location.search);
        const busqueda = urlParams.get('busqueda');
        
        if (busqueda) {
            const input = document.getElementById('inputBusqueda');
            input.value = busqueda;
            
            // Llamamos a tu función que ya existe en busqueda_paciente.js
            if (typeof realizarBusqueda === 'function') {
                realizarBusqueda();
            }
        }
    });
    </script>
</body>
</html>
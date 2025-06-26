<?php
// detalle-propietario.php

// ======================================================
// 1. Configuración de la Base de Datos (Tu conexión a Hostinger)
// ======================================================
$dbHost = "srv805.hstgr.io";
$dbUser = "u666383048_clinica";
$dbPass = "9~o0jY:Xw";
$dbName = "u666383048_clinica";
$dbPort = 3306;

// Establecer conexión con la base de datos
$conn = new mysqli($dbHost, $dbUser, $dbPass, $dbName, $dbPort);
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}
$conn->set_charset("utf8mb4"); // Asegurarse de que la conexión usa UTF-8

// ======================================================
// 2. Obtención de IDs y Preparación de Mensajes
// ======================================================
$paciente_principal_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

$mensaje_paciente_principal = "";
$mensaje_consulta = ""; // Mensaje para operaciones con la tabla consultas_medicas

// Procesar mensajes de éxito/error de `agregar-consulta.php`
if (isset($_GET['success_consulta'])) { // Cambiado de 'mascota' a 'consulta'
    $mensaje_consulta = htmlspecialchars($_GET['success_consulta']);
} elseif (isset($_GET['error_consulta'])) { // Cambiado de 'mascota' a 'consulta'
    $mensaje_consulta = htmlspecialchars($_GET['error_consulta']);
}

// ======================================================
// 3. Obtener Información del Paciente Principal (tabla 'pacientes')
// ======================================================
$paciente_principal_data = null;
if ($paciente_principal_id === 0) {
    die("ID de paciente principal no proporcionado.");
}

// Seleccionar solo las columnas que pertenecen a la tabla 'pacientes'
$sql_paciente_principal = "SELECT id, nombre, direccion, telefono, dni, fechaNacimiento, nacionalidad, sexo FROM pacientes WHERE id = ?";
$stmt_paciente_principal = $conn->prepare($sql_paciente_principal);
if ($stmt_paciente_principal === false) {
    die("Error en la preparación de la consulta de pacientes: " . $conn->error);
}
$stmt_paciente_principal->bind_param("i", $paciente_principal_id);
$stmt_paciente_principal->execute();
$result_paciente_principal = $stmt_paciente_principal->get_result();

if ($result_paciente_principal->num_rows > 0) {
    $paciente_principal_data = $result_paciente_principal->fetch_assoc();
} else {
    die("No se encontró el paciente principal con ID: " . htmlspecialchars($paciente_principal_id));
}
$stmt_paciente_principal->close();

// ======================================================
// 4. Obtener "Consultas Médicas" (Entradas de la tabla 'consultas_medicas')
// ======================================================
$consultas_medicas_del_paciente = [];
// Columnas esperadas de la tabla 'consultas_medicas'
// id, paciente_id, nombre_consulta, diagnostico_breve, sexo, especialidad, fecha_consulta, diagnostico_detallado
$sql_consultas = "SELECT id, nombre_consulta, diagnostico_breve, sexo, especialidad, fecha_consulta, diagnostico_detallado FROM consultas_medicas WHERE paciente_id = ? ORDER BY id DESC";
$stmt_consultas = $conn->prepare($sql_consultas);
if ($stmt_consultas === false) {
    error_log("Error en la preparación de la consulta de consultas médicas: " . $conn->error);
    echo "<p class='error'>Error interno al cargar las consultas del paciente.</p>";
} else {
    $stmt_consultas->bind_param("i", $paciente_principal_id);
    $stmt_consultas->execute();
    $result_consultas_paciente = $stmt_consultas->get_result();
    while ($row = $result_consultas_paciente->fetch_assoc()) {
        $consultas_medicas_del_paciente[] = $row;
    }
    $stmt_consultas->close();
}




// ======================================================
// 5. Procesar Formularios POST (Solo la edición del paciente principal aquí)
// ======================================================
// NOTA: La lógica de agregar/editar/eliminar consultas se procesa en 'agregar-consulta.php'
// y 'eliminar-consulta.php' respectivamente.
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Aseguramos que $paciente_principal_id esté disponible.
    // Esto es crucial para la cláusula WHERE de las consultas.
    // Si tu ID viene por GET en la URL (ej. detalle-propietario.php?id=123),
    // se debe obtener al inicio del script, antes de este bloque POST.
    // Si tu formulario de edición lo envía en un campo oculto, obténlo de $_POST.
    if (!isset($paciente_principal_id)) {
        $paciente_principal_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
    }

    if (isset($_POST['editar_paciente_principal'])) { // Nombre del botón de submit en el modal de edición del paciente principal
        // Sanear y obtener los datos del formulario del paciente principal
        $nuevo_nombre = htmlspecialchars(trim($_POST['nombre'] ?? ''));
        $nueva_direccion = htmlspecialchars(trim($_POST['direccion'] ?? ''));
        $nuevo_telefono = htmlspecialchars(trim($_POST['telefono'] ?? ''));
        $nuevo_dni = htmlspecialchars(trim($_POST['dni'] ?? ''));
        $nueva_fechaNacimiento = htmlspecialchars(trim($_POST['fechaNacimiento'] ?? ''));
        $nueva_nacionalidad = htmlspecialchars(trim($_POST['nacionalidad'] ?? ''));
        $nuevo_sexo = htmlspecialchars(trim($_POST['sexo'] ?? ''));
        
        // VALIDACIÓN BÁSICA
        if (empty($paciente_principal_id) || $paciente_principal_id <= 0) {
            $mensaje_paciente_principal = "Error: ID de paciente principal no válido para la edición.";
        } elseif (empty($nuevo_nombre) || empty($nuevo_dni)) {
            $mensaje_paciente_principal = "Error: Nombre y DNI del paciente principal son obligatorios.";
        } else {
            // Prepara la consulta SQL para actualizar los datos del paciente principal
            $sql_update_paciente = "UPDATE pacientes SET
                nombre = ?,
                direccion = ?,
                telefono = ?,
                dni = ?,
                fechaNacimiento = ?,
                nacionalidad = ?,
                sexo = ?
                WHERE id = ?";

            $stmt_update_paciente = $conn->prepare($sql_update_paciente);
            
            // ************ MANEJO DE ERROR CRÍTICO PARA prepare() ************
            if ($stmt_update_paciente === false) {
                // Capturamos el error de MySQL si la preparación falla
                $mensaje_paciente_principal = "Error al preparar la actualización del paciente principal: " . $conn->error;
                // Opcional: registrar el error para depuración
                // error_log("SQL Prepare Error (detalle-propietario.php): " . $conn->error);
            } else {
                // La preparación fue exitosa, ahora vinculamos los parámetros
                // sssssssi (7 strings, 1 integer para el ID)
                $stmt_update_paciente->bind_param("sssssssi",
                    $nuevo_nombre,
                    $nueva_direccion,
                    $nuevo_telefono,
                    $nuevo_dni,
                    $nueva_fechaNacimiento,
                    $nueva_nacionalidad,
                    $nuevo_sexo,
                    $paciente_principal_id // Este es el 'i' (integer)
                );

                if ($stmt_update_paciente->execute()) {
                    $mensaje_paciente_principal = "Datos del paciente principal actualizados correctamente.";
                    
                    // Refrescar $paciente_principal_data para que la página muestre los datos actualizados
                    // ************ CORRECCIÓN DEL ESPACIO AQUÍ: sexo FROM ************
                    $sql_refresh_paciente = "SELECT id, nombre, direccion, telefono, dni, fechaNacimiento, nacionalidad, sexo FROM pacientes WHERE id = ?";
                    $stmt_refresh_paciente = $conn->prepare($sql_refresh_paciente);

                    if ($stmt_refresh_paciente === false) {
                        // Manejo de error si la consulta de refresco falla
                        $mensaje_paciente_principal .= " (Sin embargo, hubo un error al refrescar los datos mostrados: " . $conn->error . ")";
                        // error_log("SQL Refresh Prepare Error (detalle-propietario.php): " . $conn->error);
                    } else {
                        $stmt_refresh_paciente->bind_param("i", $paciente_principal_id);
                        $stmt_refresh_paciente->execute();
                        $result_refresh_paciente = $stmt_refresh_paciente->get_result();
                        if ($result_refresh_paciente->num_rows > 0) {
                            $paciente_principal_data = $result_refresh_paciente->fetch_assoc();
                        }
                        $stmt_refresh_paciente->close();
                    }
                } else {
                    // Si la ejecución falla, obtenemos el error detallado de la sentencia preparada
                    $mensaje_paciente_principal = "Error al actualizar los datos del paciente principal: " . $stmt_update_paciente->error;
                    // error_log("SQL Execute Error (detalle-propietario.php): " . $stmt_update_paciente->error);
                }
                $stmt_update_paciente->close();
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalles del Paciente</title>
    <link rel="stylesheet" href="../css/ver-detalle.css">
    <link rel="stylesheet" href="../css/agendar-consulta.css">
    <link rel="shortcut icon" href="../img/favicon2.ico" type="image/x-icon"> 
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    <style>
        /* Tu bloque de CSS para los modales va aquí... perfecto como está */
        /* ESTILOS ADICIONALES PARA LOS NUEVOS MODALES Y AJUSTES DE Z-INDEX */
          body {
            background-color: #75baf3; /* Un tono de celeste claro */
        }
        .modal-overlay {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0,0,0,0.6);
            justify-content: center;
            align-items: center;
            backdrop-filter: blur(5px);
        }
        .modal-overlay.active { display: flex; }
        .modal {
            background-color: #fefefe;
            padding: 17px;
            border-radius: 12px;
            box-shadow: 0 8px 16px rgba(0,0,0,0.25);
            max-width: 500px;
            width: 90%;
            position: relative;
            animation: fadeIn 0.3s ease-out;
        }
        #mascotaModalOverlay { z-index: 1001; }
        .modal-close-button {
            position: absolute;
            top: 15px;
            right: 15px;
            font-size: 24px;
            font-weight: bold;
            color: #aaa;
            cursor: pointer;
            border: none;
            background: none;
            transition: color 0.3s ease;
        }
        .modal-close-button:hover { color: #333; }
        .modal h2 {
            margin-top: 0;
            padding-bottom: 10px;
            border-bottom: 1px solid #eee;
            margin-bottom: 20px;
        }
        .modal label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #555;
        }
        .modal input[type="text"], .modal input[type="date"], .modal select, .modal textarea {
            width: calc(100% - 20px);
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ddd;
            border-radius: 6px;
            font-size: 1rem;
            box-sizing: border-box;
        }
        .modal button[type="submit"] {
            background-color: #1976d2;
            color: white;
            padding: 12px 20px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 1rem;
            transition: background-color 0.3s ease;
        }
        .modal button[type="submit"]:hover { background-color: #1565c0; }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .tarjeta, .cuadro-mascotas, #form-editar-cliente-principal {
            position: relative;
            z-index: 1;
        }
        #form-editar-cliente-principal { display: none; }
    </style>
</head>
<body>
<div class="contenedor">
    <div class="patient-dashboard-card">
        <h2 class="dashboard-title">Información del Paciente: <?php echo htmlspecialchars($paciente_principal_data['nombre'] ?? 'N/A'); ?></h2>

        <?php if (!empty($mensaje_paciente_principal ?? '')) { echo "<p class='mensaje'>$mensaje_paciente_principal</p>"; } ?>
        <?php if (!empty($mensaje_consulta ?? '')) { echo "<p class='mensaje'>$mensaje_consulta</p>"; } ?>

        <div class="patient-tabs">
            <button class="tab-button active" data-tab="detalles-paciente">
                <i class="fas fa-user-alt"></i> Detalles del Paciente
            </button>
            <button class="tab-button" data-tab="otras-consultas">
                <i class="fas fa-notes-medical"></i> Historial de Consultas
            </button>
            <button class="tab-button" data-tab="agregar-consulta-tab">
                <i class="fas fa-plus-circle"></i> Agregar Consulta
            </button>
        </div>

        <div id="detalles-paciente" class="tab-content active">
            <h3 class="section-title"><i class="fas fa-address-card"></i> Datos del Paciente Principal</h3>
            <div class="patient-details-grid" id="tarjeta-para-descargar">
                <p><strong>ID:</strong> <?php echo htmlspecialchars($paciente_principal_data['id'] ?? 'N/A'); ?></p>
                <p><strong>Nombre del paciente:</strong> <?php echo htmlspecialchars($paciente_principal_data['nombre'] ?? 'N/A'); ?></p>
                <p><strong>Dirección:</strong> <?php echo htmlspecialchars($paciente_principal_data['direccion'] ?? 'N/A'); ?></p>
                <p><strong>Teléfono:</strong> <?php echo htmlspecialchars($paciente_principal_data['telefono'] ?? 'N/A'); ?></p>
                <p><strong>DNI:</strong> <?php echo htmlspecialchars($paciente_principal_data['dni'] ?? 'N/A'); ?></p>
                <p><strong>Fecha de nacimiento:</strong> <?php echo htmlspecialchars($paciente_principal_data['fechaNacimiento'] ?? 'N/A'); ?></p>
                <p><strong>Nacionalidad:</strong> <?php echo htmlspecialchars($paciente_principal_data['nacionalidad'] ?? 'N/A'); ?></p>
                <p><strong>Sexo:</strong> <?php echo htmlspecialchars($paciente_principal_data['sexo'] ?? 'N/A'); ?></p>
              
            </div>
            <div class="button-container">
                <button class="btn btn-primary" id="openEditPacienteModalBtn"><i class="fas fa-edit"></i> Editar Paciente</button>
                <a href="descargar_pdf.php?id=<?php echo htmlspecialchars($paciente_principal_data['id'] ?? ''); ?>" class="btn btn-info" target="_blank"><i class="fas fa-file-pdf"></i> Descargar en PDF</a>
                <button class="btn btn-secondary" id="descargarTarjetaBtn" onclick="descargarTarjeta()"><i class="fas fa-image"></i> Descargar imagen</button>
            </div>
        </div>

        <div id="otras-consultas" class="tab-content">
            <h3 class="section-title"><i class="fas fa-history"></i> Historial de Consultas Médicas</h3>
            <ul class="consultas-list" id="lista-consultas">
                <?php if ($result_consultas_paciente && $result_consultas_paciente->num_rows > 0): ?>
                    <?php $result_consultas_paciente->data_seek(0); // Asegurarse de que el puntero esté al principio ?>
                    <?php while ($consulta_item = $result_consultas_paciente->fetch_assoc()): ?>
                        <li class="consulta-item">
                            <div class="consulta-info">
                                <p><strong>Nombre de consulta:</strong> <?php echo htmlspecialchars($consulta_item['nombre_consulta'] ?? 'N/A'); ?></p>
                                <p><strong>Diagnóstico Breve:</strong> <?php echo htmlspecialchars(substr($consulta_item['diagnostico_breve'] ?? 'N/A', 0, 70)); ?>...</p>
                                <p><strong>Especialidad:</strong> <?php echo htmlspecialchars($consulta_item['especialidad'] ?? 'N/A'); ?></p>
                                <p><strong>Fecha de Consulta:</strong> <?php echo htmlspecialchars($consulta_item['fecha_consulta'] ?? 'N/A'); ?></p>
                            </div>
                            <div class="consulta-actions">
                                <button class="btn btn-info btn-sm btn-info-consulta"
                                        data-id="<?php echo htmlspecialchars($consulta_item['id'] ?? ''); ?>"
                                        data-nombre-consulta="<?php echo htmlspecialchars($consulta_item['nombre_consulta'] ?? ''); ?>"
                                        data-diagnostico-breve="<?php echo htmlspecialchars($consulta_item['diagnostico_breve'] ?? ''); ?>"
                                        data-sexo="<?php echo htmlspecialchars($consulta_item['sexo'] ?? ''); ?>"
                                        data-especialidad="<?php echo htmlspecialchars($consulta_item['especialidad'] ?? ''); ?>"
                                        data-fecha-consulta="<?php echo htmlspecialchars($consulta_item['fecha_consulta'] ?? ''); ?>"
                                        data-diagnostico-detallado="<?php echo htmlspecialchars($consulta_item['diagnostico_detallado'] ?? ''); ?>">
                                    Más información
                                </button>
                                <button class="btn btn-danger btn-sm delete-consulta-btn"
                                        data-consulta-id="<?php echo htmlspecialchars($consulta_item['id'] ?? ''); ?>"
                                        data-nombre-consulta="<?php echo htmlspecialchars($consulta_item['nombre_consulta'] ?? ''); ?>">
                                    Eliminar
                                </button>
                            </div>
                        </li>
                    <?php endwhile; ?>
                <?php else: ?>
                    <p>No hay consultas médicas registradas para este paciente principal.</p>
                <?php endif; ?>
            </ul>
        </div>







        
<div id="agregar-consulta-tab" class="tab-content">
    <?php
    // Variables para almacenar los mensajes
    $mensaje_exito = '';
    $mensaje_error = '';

    // Leer mensajes de éxito/error para las consultas (desde agregar-consulta.php)
    if (isset($_GET['success_mascota'])) {
        $mensaje_exito = htmlspecialchars($_GET['success_mascota']);
    } elseif (isset($_GET['error_mascota'])) {
        $mensaje_error = htmlspecialchars($_GET['error_mascota']);
    }

    // Leer mensajes de éxito/error para el cliente principal (si es que esta página los maneja también)
    // Nota: Si ambos, success_mascota y success_cliente, llegan al mismo tiempo,
    // el último que se asigne será el que se muestre. Podrías ajustarlo si necesitas mostrar ambos.
    if (isset($_GET['success_cliente'])) {
        $mensaje_exito = htmlspecialchars($_GET['success_cliente']); 
    } elseif (isset($_GET['error_cliente'])) {
        $mensaje_error = htmlspecialchars($_GET['error_cliente']); 
    }
    ?>

    <div class="messages-container">
        <?php if (!empty($mensaje_exito)): ?>
            <div class="alert success-alert">
                <?php echo $mensaje_exito; ?>
                <span class="close-btn" onclick="this.parentElement.style.display='none';">&times;</span>
            </div>
        <?php endif; ?>

        <?php if (!empty($mensaje_error)): ?>
            <div class="alert error-alert">
                <?php echo $mensaje_error; ?>
                <span class="close-btn" onclick="this.parentElement.style.display='none';">&times;</span>
            </div>
        <?php endif; ?>
    </div>

    <h3 class="section-title"><i class="fas fa-plus-square"></i> Registrar Nueva Consulta</h3>
    <form id="form-agregar-consulta-directo" method="POST" action="../php/agregar-consulta.php">
        <input type="hidden" name="mascota_form_action_type" value="agregar_mascota"> <input type="hidden" name="propietario_id" value="<?php echo htmlspecialchars($paciente_principal_id ?? ''); ?>"> <div class="form-group">
            <label for="nueva_consulta_nombre_input">Nombre de Consulta:</label>
            <input type="text" id="nueva_consulta_nombre_input" name="nombre_consulta" required>
        </div>

        <div class="form-group">
            <label for="nueva_consulta_diagnostico_breve_input">Diagnóstico (breve):</label>
            <input type="text" id="nueva_consulta_diagnostico_breve_input" name="diagnostico_breve" required>
        </div>

        <div class="form-group">
            <label for="nueva_consulta_sexo_input">Sexo (de la consulta):</label>
            <select id="nueva_consulta_sexo_input" name="sexo" required>
                <option value="">Seleccione</option>
                <option value="masculino">Masculino</option>
                <option value="femenino">Femenino</option>
                <option value="no aplica">No aplica</option>
            </select>
        </div>

        <div class="form-group">
            <label for="nueva_consulta_especialidad_input">Especialidad:</label>
            <input type="text" id="nueva_consulta_especialidad_input" name="especialidad" required>
        </div>

        <div class="form-group">
            <label for="nueva_consulta_fecha_input">Fecha de la Consulta:</label>
            <input type="date" id="nueva_consulta_fecha_input" name="fecha_consulta" required>
        </div>

        <div class="form-group full-width">
            <label for="nueva_consulta_diagnostico_detallado_input">Diagnóstico Detallado:</label>
            <textarea id="nueva_consulta_diagnostico_detallado_input" name="diagnostico_detallado" rows="4" placeholder="Especifica el diagnóstico o cualquier detalle adicional..."></textarea>
        </div>

        <div class="form-group full-width button-group">
            <button type="submit" class="btn btn-success"><i class="fas fa-save"></i> Guardar Nueva Consulta</button>
        </div>
    </form>
</div>












    <div class="volver-container">
        <a href="ventanas.php" class="btn btn-secondary btn-volver"><i class="fas fa-arrow-left"></i> Volver a gestión de pacientes</a>
    </div>
</div>

<div class="modal-overlay" id="editPacientePrincipalModalOverlay">
    <div class="modal">
        <button class="modal-close-button close-button" id="closeEditPacientePrincipalModalBtn">X</button>
        <h2><i class="fas fa-edit"></i> Editar Datos del Paciente Principal</h2>
        <form id="formEditarPacientePrincipal" method="POST" action="detalle-propietario.php?id=<?php echo htmlspecialchars($paciente_principal_data['id'] ?? ''); ?>">
            <input type="hidden" name="editar_paciente_principal" value="1"> <div class="form-group">
                <label for="pacienteNombreInput">Nombre del paciente:</label>
                <input type="text" id="pacienteNombreInput" name="nombre" value="<?php echo htmlspecialchars($paciente_principal_data['nombre'] ?? ''); ?>" required>
            </div>
            <div class="form-group">
                <label for="pacienteDniInput">DNI:</label>
                <input type="text" id="pacienteDniInput" name="dni" value="<?php echo htmlspecialchars($paciente_principal_data['dni'] ?? ''); ?>" required>
            </div>
            <div class="form-group">
                <label for="pacienteDireccionInput">Dirección:</label>
                <input type="text" id="pacienteDireccionInput" name="direccion" value="<?php echo htmlspecialchars($paciente_principal_data['direccion'] ?? ''); ?>" required>
            </div>
            <div class="form-group">
                <label for="pacienteTelefonoInput">Teléfono:</label>
                <input type="text" id="pacienteTelefonoInput" name="telefono" value="<?php echo htmlspecialchars($paciente_principal_data['telefono'] ?? ''); ?>" required>
            </div>
            <div class="form-group">
                <label for="pacienteFechaNacimientoInput">Fecha de Nacimiento:</label>
                <input type="date" id="pacienteFechaNacimientoInput" name="fechaNacimiento" value="<?php echo htmlspecialchars($paciente_principal_data['fechaNacimiento'] ?? ''); ?>" required>
            </div>
            <div class="form-group">
                <label for="pacienteNacionalidadInput">Nacionalidad:</label>
                <input type="text" id="pacienteNacionalidadInput" name="nacionalidad" value="<?php echo htmlspecialchars($paciente_principal_data['nacionalidad'] ?? ''); ?>" required>
            </div>
            <div class="form-group">
                <label for="pacienteSexoInput">Sexo:</label>
                <select id="pacienteSexoInput" name="sexo" required>
                    <option value="masculino" <?php echo (($paciente_principal_data['sexo'] ?? '') === 'masculino') ? 'selected' : ''; ?>>Masculino</option>
                    <option value="femenino" <?php echo (($paciente_principal_data['sexo'] ?? '') === 'femenino') ? 'selected' : ''; ?>>Femenino</option>
                </select>
            </div>
            <div class="form-group">
                <label for="pacienteFechaSeguimientoInicioInput">Fecha de Seguimiento:</label>
                <input type="date" id="pacienteFechaSeguimientoInicioInput" name="fechaSeguimientoInicio" value="<?php echo htmlspecialchars($paciente_principal_data['fechaSeguimientoInicio'] ?? ''); ?>" required>
            </div>
            <button type="submit" id="submitEditPacientePrincipalBtn" class="btn btn-success"><i class="fas fa-save"></i> Guardar Cambios</button>
        </form>
    </div>
</div>

<div id="detallesConsultaModalOverlay" class="modal-overlay">
    <div class="modal">
        <button class="modal-close-button close-button" id="closeDetallesConsultaModalBtn">&times;</button>
        <h2><i class="fas fa-info-circle"></i> Detalles de la Consulta</h2>
        <div class="modal-body" id="detalles-consulta-contenido">
            <p><strong>ID de Consulta:</strong> <span id="detallesConsultaIdSpan"></span></p>
            <p><strong>Nombre de Consulta:</strong> <span id="detalle-nombre-consulta"></span></p>
            <p><strong>Diagnóstico Breve:</strong> <span id="detalle-diagnostico-breve"></span></p>
            <p><strong>Sexo:</strong> <span id="detalle-sexo-consulta"></span></p>
            <p><strong>Especialidad:</strong> <span id="detalle-especialidad-consulta"></span></p>
            <p><strong>Fecha de Consulta:</strong> <span id="detalle-fecha-consulta"></span></p>
            <p><strong>Diagnóstico Detallado:</strong> <span id="detalle-diagnostico-detallado"></span></p>
    
        </div>
        <div class="modal-footer button-container" style="justify-content: center; border-top: none; padding-top: 0;">
            <button id="editThisConsultaButton" class="btn btn-primary"><i class="fas fa-edit"></i> Editar esta consulta</button>
        </div>



 <div id="historial-seguimientos-section" style="margin-top: 20px;">
    
                <h3>Historial de Visitas:</h3>
                <h3><i class="fas fa-notes-medical"></i> Seguimientos de la Consulta:</h3>
                <button id="addSeguimientoBtn" class="btn btn-success btn-sm mb-2"><i class="fas fa-plus"></i> Registrar Nuevo Seguimiento</button>

                <div id="addSeguimientoModalOverlay" class="modal-overlay" style="display: none;">
    <div class="modal">
        <button class="modal-close-button close-button" id="closeAddSeguimientoModalBtn">&times;</button>
        <h2><i class="fas fa-plus"></i> Registrar Nuevo Seguimiento</h2>
        <div class="modal-body">
            <form id="formAddSeguimiento">
                <input type="hidden" name="consulta_id_seguimiento" id="consultaIdSeguimientoInput">
                
                <div class="form-group">
                    <label for="fechaSeguimiento">Fecha del Seguimiento:</label>
                    <input type="date" class="form-control" id="fechaSeguimiento" name="fecha_seguimiento" required>
                </div>
                <div class="form-group">
                    <label for="descripcionSeguimiento">Descripción del Seguimiento:</label>
                    <textarea class="form-control" id="descripcionSeguimiento" name="descripcion_seguimiento" rows="5" required></textarea>
                </div>
                <div class="form-group">
                    <label for="doctorSeguimiento">Doctor (Opcional):</label>
                    <input type="text" class="form-control" id="doctorSeguimiento" name="doctor_seguimiento" placeholder="Nombre del doctor">
                </div>
                
                <div class="button-container" style="margin-top: 20px;">
                    <button type="submit" class="btn btn-success"><i class="fas fa-save"></i> Guardar Seguimiento</button>
                    <button type="button" class="btn btn-secondary" id="cancelAddSeguimientoBtn"><i class="fas fa-times-circle"></i> Cancelar</button>
                </div>
            </form>
        </div>
    </div>
</div>
                <div id="seguimientos-list-container">
                    <ul id="detalle-historial-visitas" class="list-group">
                        </ul>
                    <p id="noSeguimientosMessage" style="display: none; margin-top: 10px; color: #666;">No hay seguimientos registrados para esta consulta.</p>
                </div>
            </div>
            </div>
        <div class="modal-footer button-container" style="justify-content: center; border-top: none; padding-top: 0;">
    
        </div>
</div>


</div>










<div class="modal-overlay" id="consultaModalOverlay"> <div class="modal">
        <button class="modal-close-button close-button" id="closeConsultaModalBtn">X</button> <h2 id="consultaModalTitle"><i class="fas fa-notes-medical"></i></h2>
        <form id="consultaForm" method="POST" action="../php/agregar-consulta.php"> <input type="hidden" id="consulta_form_action_type" name="form_action_type" value=""> <input type="hidden" id="consulta_id_edit" name="consulta_id_edit" value=""> <input type="hidden" id="pacienteIdInput" name="paciente_id" value="<?php echo htmlspecialchars($paciente_principal_id ?? ''); ?>"> <label for="consulta_nombre_input">Nombre de Consulta:</label> <input type="text" id="consulta_nombre_input" name="nombre_consulta" required> <label for="consulta_diagnostico_breve_input">Diagnóstico (breve):</label> <input type="text" id="consulta_diagnostico_breve_input" name="diagnostico_breve" required>

            <label for="consulta_sexo_input">Sexo (de la consulta):</label> <select id="consulta_sexo_input" name="sexo" required> <option value="">Seleccione</option>
                <option value="masculino">Masculino</option>
                <option value="femenino">Femenino</option>
                <option value="no aplica">No aplica</option>
            </select>

            <label for="consulta_especialidad_input">Especialidad:</label> <input type="text" id="consulta_especialidad_input" name="especialidad" required>

            <label for="consulta_fecha_input">Fecha de la Consulta:</label> <input type="date" id="consulta_fecha_input" name="fecha_consulta" required>

            <label for="consulta_diagnostico_detallado_input">Diagnóstico Detallado:</label> <textarea id="consulta_diagnostico_detallado_input" name="diagnostico_detallado" rows="4" placeholder="Especifica el diagnóstico o cualquier detalle adicional..."></textarea>

            <button type="submit" id="submitConsultaModalBtn" class="btn btn-success"></button> </form>
    </div>
</div>

<script src="https://html2canvas.hertzen.com/dist/html2canvas.min.js"></script>
<script src="procesar_seguimiento.js"></script>
<script src="../js/detalle-propietario.js"></script>

<script>
    // Lógica para mostrar el modal de detalles de consulta si consulta_id está en la URL
    document.addEventListener('DOMContentLoaded', () => {
        console.log("Script PHP/JS In-line: DOMContentLoaded disparado para lógica específica de PHP.");

        // Recuperar el ID de la consulta y el ID del paciente principal de la URL
        const urlParams = new URLSearchParams(window.location.search);
        const consultaIdFromUrl = urlParams.get('consulta_id');
        const pacientePrincipalIdFromUrl = urlParams.get('id'); // ID del paciente principal

        if (consultaIdFromUrl && pacientePrincipalIdFromUrl) {
            console.log(`Script PHP/JS In-line: consulta_id=${consultaIdFromUrl} y id (paciente principal)=${pacientePrincipalIdFromUrl} encontrados en la URL.`);
            // Realizar una solicitud AJAX para obtener los detalles de la consulta
            fetch(`../php/get-consulta-details.php?consulta_id=${consultaIdFromUrl}&paciente_id=${pacientePrincipalIdFromUrl}`)
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        console.log("Script PHP/JS In-line: Datos de consulta obtenidos por AJAX:", data.consulta);
                        // Asegúrate de que openDetallesConsultaModal esté definida en detalle-propietario.js
                        openDetallesConsultaModal(data.consulta);
                        // Limpiar los parámetros de la URL para evitar que el modal se abra en futuras recargas
                        const newUrl = new URL(window.location.href);
                        newUrl.searchParams.delete('consulta_id');
                        // No eliminar el 'id' del paciente principal, ya que es necesario para la página
                        window.history.replaceState({}, document.title, newUrl.toString());
                    } else {
                        console.error("Script PHP/JS In-line: Error al obtener detalles de la consulta:", data.message);
                    }
                })
                .catch(error => {
                    console.error("Script PHP/JS In-line: Error en la solicitud AJAX para detalles de consulta:", error);
                });
        } else {
            console.log("Script PHP/JS In-line: No se encontraron 'consulta_id' o 'id' en la URL. No se abrirá automáticamente el modal de detalles.");
        }

        console.log("Script PHP/JS In-line: Lógica de inicialización (DOMContentLoaded) ejecutada.");
    });
</script>

</body>
</html>
<?php
$dbHost = "srv805.hstgr.io"; // Host proporcionado por Hostinger
$dbUser = "u666383048_clinica"; // Usuario de la base de datos
$dbPass = "9~o0jY:Xw"; // Contraseña del usuario
$dbName = "u666383048_clinica"; // Nombre de la base de datos
$dbPort = 3306; // Puerto de la base de datos (generalmente 3306 para MySQL)

// Establecer conexión con la base de datos
$conn = new mysqli($dbHost, $dbUser, $dbPass, $dbName, $dbPort);
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// ID del cliente/paciente principal
$cliente_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
// ID de la "mascota" (que ahora representa una consulta/episodio de enfermedad)
$mascota_id = isset($_GET['mascota_id']) ? intval($_GET['mascota_id']) : 0;

// Variables para manejar mensajes
$mensaje_cliente = "";
$mensaje_mascota = ""; // Mensaje para operaciones con la tabla mascotas

/*Sección 1: Visualizar Información de Clientes (Pacientes)*/

$sql_cliente = "SELECT * FROM clientes WHERE id = ?";
$stmt_cliente = $conn->prepare($sql_cliente);
if ($stmt_cliente === false) {
    die("Error en la preparación de la consulta de clientes: " . $conn->error);
}
$stmt_cliente->bind_param("i", $cliente_id);
$stmt_cliente->execute();
$result_cliente = $stmt_cliente->get_result();

if ($result_cliente->num_rows > 0) {
    $cliente_data = $result_cliente->fetch_assoc(); // Datos del cliente principal
} else {
    die("No se encontró el paciente.");
}
$stmt_cliente->close();
/* Sección 2: Obtener "OTRAS CONSULTAS DEL PACIENTE" (Entradas de la tabla 'mascotas')
*/
$result_mascotas_paciente = null; // Inicializar a null

$sql_mascotas = "SELECT * FROM mascotas WHERE propietario_id = ? ORDER BY id DESC";
$stmt_mascotas = $conn->prepare($sql_mascotas);
if ($stmt_mascotas === false) {
    error_log("Error en la preparación de la consulta de mascotas: " . $conn->error);
} else {
    $stmt_mascotas->bind_param("i", $cliente_id);
    $stmt_mascotas->execute();
    $result_mascotas_paciente = $stmt_mascotas->get_result();
    $stmt_mascotas->close();
}

/* Sección 3: Obtener detalles de una "MASCOTA" ESPECÍFICA (para VER/EDITAR en modal)*/
$mascota_detalles = null;
if ($mascota_id > 0) {
    $sql_mascota_especifica = "SELECT * FROM mascotas WHERE id = ? AND propietario_id = ?";
    $stmt_mascota_especifica = $conn->prepare($sql_mascota_especifica);
    if ($stmt_mascota_especifica === false) {
        error_log("Error en la preparación de la consulta de mascota específica: " . $conn->error);
    } else {
        $stmt_mascota_especifica->bind_param("ii", $mascota_id, $cliente_id);
        $stmt_mascota_especifica->execute();
        $result_mascota_especifica = $stmt_mascota_especifica->get_result();
        if ($result_mascota_especifica->num_rows > 0) {
            $mascota_detalles = $result_mascota_especifica->fetch_assoc();
        }
        $stmt_mascota_especifica->close();
    }
}
/* Sección 4: Procesar Formularios POST*/
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // --- Editar Cliente Principal ---
    if (isset($_POST['editar_cliente_principal'])) {
        $nuevo_nombre = $_POST['nombre'];
        $nueva_direccion = $_POST['direccion'];
        $nuevo_telefono = $_POST['telefono'];
        $nuevo_dni = $_POST['dni'];
        $nuevo_doctor = $_POST['doctor'];
        $nueva_fechaNacimiento = $_POST['fechaNacimiento'];
        $nueva_nacionalidad = $_POST['nacionalidad'];
        $nuevo_diagnostico = $_POST['diagnostico'];
        $nuevo_sexo = $_POST['sexo'];
        $nueva_especialidad = $_POST['especialidad'];
        $nueva_fechaSeguimientoInicio = $_POST['fechaSeguimientoInicio'];
        $nueva_descripcion = $_POST['descripcion'];

        $sql_update_cliente = "UPDATE clientes SET
            nombre = ?, direccion = ?, telefono = ?, dni = ?, doctor = ?,
            fechaNacimiento = ?, nacionalidad = ?, diagnostico = ?, sexo = ?,
            especialidad = ?, fechaSeguimientoInicio = ?, descripcion = ?
            WHERE id = ?";

        $stmt_update_cliente = $conn->prepare($sql_update_cliente);
        if ($stmt_update_cliente === false) {
            die("Error al preparar la actualización del cliente: " . $conn->error);
        }
        $stmt_update_cliente->bind_param("ssssssssssssi",
            $nuevo_nombre, $nueva_direccion, $nuevo_telefono, $nuevo_dni, $nuevo_doctor,
            $nueva_fechaNacimiento, $nueva_nacionalidad, $nuevo_diagnostico, $nuevo_sexo,
            $nueva_especialidad, $nueva_fechaSeguimientoInicio, $nueva_descripcion, $cliente_id
        );

        if ($stmt_update_cliente->execute()) {
            $mensaje_cliente = "Datos del paciente principal actualizados correctamente.";
            // Refrescar $cliente_data para que la página muestre los datos actualizados
            $sql_refresh_cliente = "SELECT * FROM clientes WHERE id = ?";
            $stmt_refresh_cliente = $conn->prepare($sql_refresh_cliente);
            $stmt_refresh_cliente->bind_param("i", $cliente_id);
            $stmt_refresh_cliente->execute();
            $result_refresh_cliente = $stmt_refresh_cliente->get_result();
            if ($result_refresh_cliente->num_rows > 0) {
                $cliente_data = $result_refresh_cliente->fetch_assoc();
            }
            $stmt_refresh_cliente->close();
        } else {
            $mensaje_cliente = "Error al actualizar los datos del paciente principal: " . $stmt_update_cliente->error;
        }
        $stmt_update_cliente->close();
    }

    // --- Agregar Nueva "Mascota" (Nueva Consulta/Episodio) ---
    if (isset($_POST['agregar_mascota'])) {
        $mascota_nombre = $_POST['mascota_nombre'];
        $mascota_nacionalidad = $_POST['mascota_nacionalidad'];
        $mascota_diagnostico = $_POST['mascota_diagnostico'];
        $mascota_sexo = $_POST['mascota_sexo'];
        $mascota_especialidad = $_POST['mascota_especialidad'];
        $mascota_fechaNacimiento = $_POST['mascota_fechaNacimiento'];
        $propietario_id_form = isset($_POST['propietario_id']) ? intval($_POST['propietario_id']) : 0; // Usar propietario_id

        if ($propietario_id_form > 0) {
            $sql_insert_mascota = "INSERT INTO mascotas (nombre, nacionalidad, diagnostico, sexo, especialidad, fechaNacimiento, propietario_id) VALUES (?, ?, ?, ?, ?, ?, ?)";
            $stmt_insert_mascota = $conn->prepare($sql_insert_mascota);
            if ($stmt_insert_mascota === false) {
                die("Error al preparar el registro de nueva mascota: " . $conn->error);
            }
            $stmt_insert_mascota->bind_param("ssssssi",
                $mascota_nombre, $mascota_nacionalidad, $mascota_diagnostico, $mascota_sexo,
                $mascota_especialidad, $mascota_fechaNacimiento, $propietario_id_form
            );

            if ($stmt_insert_mascota->execute()) {
                $mensaje_mascota = "Nueva consulta (mascota) registrada correctamente.";
                // Redirige para refrescar la página, evitando reenvío de formulario
                header("Location: detalle-propietario.php?id=$propietario_id_form");
                exit();
            } else {
                $mensaje_mascota = "Error al registrar la nueva consulta (mascota): " . $stmt_insert_mascota->error;
            }
            $stmt_insert_mascota->close();
        } else {
            $mensaje_mascota = "No se pudo registrar la nueva consulta: ID de paciente inválido.";
        }
    }

    // --- Editar "Mascota" Específica (Consulta/Episodio) ---
    if (isset($_POST['editar_mascota'])) {
        $id_mascota_edit = isset($_POST['id_mascota_edit']) ? intval($_POST['id_mascota_edit']) : 0;
        $propietario_id_edit = isset($_POST['propietario_id_edit']) ? intval($_POST['propietario_id_edit']) : 0; // Para asegurar la pertenencia

        $mascota_nombre_edit = $_POST['mascota_nombre_edit'];
        $mascota_nacionalidad_edit = $_POST['mascota_nacionalidad_edit'];
        $mascota_diagnostico_edit = $_POST['mascota_diagnostico_edit'];
        $mascota_sexo_edit = $_POST['mascota_sexo_edit'];
        $mascota_especialidad_edit = $_POST['mascota_especialidad_edit'];
        $mascota_fechaNacimiento_edit = $_POST['mascota_fechaNacimiento_edit'];

        if ($id_mascota_edit > 0 && $propietario_id_edit == $cliente_id) { // Doble verificación para seguridad
            $sql_update_mascota = "UPDATE mascotas SET
                nombre = ?, nacionalidad = ?, diagnostico = ?, sexo = ?, especialidad = ?, fechaNacimiento = ?
                WHERE id = ? AND propietario_id = ?";
            $stmt_update_mascota = $conn->prepare($sql_update_mascota);
            if ($stmt_update_mascota === false) {
                die("Error al preparar la actualización de la mascota: " . $conn->error);
            }
            $stmt_update_mascota->bind_param("ssssssii",
                $mascota_nombre_edit, $mascota_nacionalidad_edit, $mascota_diagnostico_edit, $mascota_sexo_edit,
                $mascota_especialidad_edit, $mascota_fechaNacimiento_edit, $id_mascota_edit, $propietario_id_edit
            );

            if ($stmt_update_mascota->execute()) {
                $mensaje_mascota = "Consulta (mascota) actualizada correctamente.";
                // Redirige para refrescar la página, eliminando el parámetro mascota_id de la URL
                header("Location: detalle-propietario.php?id=$cliente_id");
                exit();
            } else {
                $mensaje_mascota = "Error al actualizar la consulta (mascota): " . $stmt_update_mascota->error;
            }
            $stmt_update_mascota->close();
        } else {
            $mensaje_mascota = "ID de consulta (mascota) inválido o no pertenece a este paciente para edición.";
        }
    }
}
// Cierre de la conexión a la base de datos al final del script
$conn->close();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalles del Paciente</title>
    <link rel="stylesheet" href="../css/ver-detalle.css">
    <link rel="stylesheet" href="../css/historial.css">
    <link rel="stylesheet" href="../css/historial2.0.css">
    
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    <style>
        /* Tu bloque de CSS para los modales va aquí... perfecto como está */
        /* ESTILOS ADICIONALES PARA LOS NUEVOS MODALES Y AJUSTES DE Z-INDEX */
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
            padding: 30px;
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


<div class="contenedor">

    <div id="tarjeta" class="tarjeta">
        <h2>Detalles del Paciente</h2>
        
        <?php if (!empty($mensaje_cliente)) { echo "<p class='mensaje'>$mensaje_cliente</p>"; } ?>
        <?php if (!empty($mensaje_mascota)) { echo "<p class='mensaje'>$mensaje_mascota</p>"; } ?>

        <p><strong>ID:</strong> <?php echo htmlspecialchars($cliente_data['id']); ?></p>
        <p><strong>Nombre del paciente:</strong> <?php echo htmlspecialchars($cliente_data['nombre']); ?></p>
        <p><strong>Dirección:</strong> <?php echo htmlspecialchars($cliente_data['direccion']); ?></p>
        <p><strong>Teléfono:</strong> <?php echo htmlspecialchars($cliente_data['telefono']); ?></p>
        <p><strong>DNI:</strong> <?php echo htmlspecialchars($cliente_data['dni']); ?></p>
        <p><strong>Fecha de nacimiento:</strong> <?php echo htmlspecialchars($cliente_data['fechaNacimiento']); ?></p>
        <p><strong>Nacionalidad:</strong> <?php echo htmlspecialchars($cliente_data['nacionalidad']); ?></p>
        <p><strong>Sexo:</strong> <?php echo htmlspecialchars($cliente_data['sexo']); ?></p>
        <p><strong>Fecha de seguimiento:</strong> <?php echo htmlspecialchars($cliente_data['fechaSeguimientoInicio']); ?></p>
        
        <div class="button-container">
            <button id="openEditPacienteModalBtn">Editar Paciente</button>
           <button> <a href="descargar_pdf.php?id=<?php echo htmlspecialchars($cliente_data['id']); ?>">Descargar en PDF</a></button    >
            <button onclick="descargarTarjeta()">Descargar imagen</button>
            <button class="open-add-mascota-modal-btn">Agregar Nueva Consulta</button>
        </div>
    </div>

    <div class="cuadro-mascotas">
        <h3>OTRAS CONSULTAS DEL PACIENTE</h3>
        <?php
        if ($result_mascotas_paciente && $result_mascotas_paciente->num_rows > 0) {
            $result_mascotas_paciente->data_seek(0);
        }
        ?>
        <?php if ($result_mascotas_paciente && $result_mascotas_paciente->num_rows > 0): ?>
            <ul>
                <?php while ($mascota_item = $result_mascotas_paciente->fetch_assoc()): ?>
                    <li>
                        <div>
                            <strong>Nombre de la consulta:</strong> <?php echo htmlspecialchars($mascota_item['nombre']); ?>,
                            <strong>Diagnóstico:</strong> <?php echo htmlspecialchars(substr($mascota_item['diagnostico'], 0, 70)); ?>...,
                            <strong>Especialidad:</strong> <?php echo htmlspecialchars($mascota_item['especialidad']); ?>
                        </div>
                        <button onclick="openDetallesConsultaModal(<?php echo htmlspecialchars(json_encode($mascota_item)); ?>)">Más información</button>
                    </li>
                <?php endwhile; ?>
            </ul>
        <?php else: ?>
            <p>No hay otras consultas registradas para este paciente.</p>
        <?php endif; ?>
    </div>

    <div class="volver">
        <a href="ventanas.php" class="btn-volver">volver a gestión de pacientes</a>
    </div>

</div> 
<div class="modal-overlay" id="detallesConsultaModalOverlay">
    <div class="modal">
        <button class="modal-close-button" id="closeDetallesConsultaModalBtn">X</button>
        <h2>Detalles de la Consulta Específica (ID paciente: <span id="detallesConsultaId"></span>)</h2>
        <p><strong>Nombre de la consulta:</strong> <span id="detallesConsultaNombre"></span></p>
        <p><strong>Nacionalidad:</strong> <span id="detallesConsultaNacionalidad"></span></p>
        <p><strong>Diagnóstico:</strong> <span id="detallesConsultaDiagnostico"></span></p>
        <p><strong>Sexo (relacionado a la consulta):</strong> <span id="detallesConsultaSexo"></span></p>
        <p><strong>Especialidad:</strong> <span id="detallesConsultaEspecialidad"></span></p>
        <p><strong>Fecha de Nacimiento (de la consulta):</strong> <span id="detallesConsultaFechaNacimiento"></span></p>
        <button id="editThisMascotaButton">Editar esta consulta</button>
    </div>
</div>

<div class="modal-overlay" id="mascotaModalOverlay">
    <div class="modal">
        <button class="modal-close-button" id="closeMascotaModalBtn">X</button>
        <h2 id="mascotaModalTitle"></h2>
        <form id="mascotaForm" method="POST">
            <input type="hidden" id="mascota_form_action_type" name="" value=""> 
            <input type="hidden" id="mascota_id_edit" name="id_mascota_edit" value="">
            <input type="hidden" id="mascota_propietario_id" name="propietario_id" value="<?php echo htmlspecialchars($cliente_id); ?>">
            <input type="hidden" name="propietario_id_edit" value="<?php echo htmlspecialchars($cliente_id); ?>"> 
            
            <label for="mascota_nombre_input">Nombre de la consulta:</label>
            <input type="text" id="mascota_nombre_input" name="mascota_nombre" required>
            <label for="mascota_nacionalidad_input">Nacionalidad:</label>
            <input type="text" id="mascota_nacionalidad_input" name="mascota_nacionalidad" required>
            <label for="mascota_diagnostico_input">Diagnóstico:</label>
            <input type="text" id="mascota_diagnostico_input" name="mascota_diagnostico" required>
            <label for="mascota_sexo_input">Sexo:</label>
            <select id="mascota_sexo_input" name="mascota_sexo" required>
                <option value="masculino">Masculino</option>
                <option value="femenino">Femenino</option> 
            </select>
            <label for="mascota_especialidad_input">Especialidad:</label>
            <input type="text" id="mascota_especialidad_input" name="mascota_especialidad" required>
            <label for="mascota_fechaNacimiento_input">Fecha de Nacimiento (de la consulta):</label>
            <input type="date" id="mascota_fechaNacimiento_input" name="mascota_fechaNacimiento" required>
            <button type="submit" id="submitMascotaModalBtn"></button>
        </form>
    </div>
</div>

<div class="modal-overlay" id="editPacientePrincipalModalOverlay">
    <div class="modal">
        <button class="modal-close-button" id="closeEditPacientePrincipalModalBtn">X</button>
        <h2>Editar Paciente Principal</h2>
        <form id="form-editar-cliente-principal" method="POST">
             </form>
    </div>
</div>
<script src="../js/detalle-propietario.js"></script>


<script>


    // Función para descargar la tarjeta del paciente como imagen
    function descargarTarjeta() {
        const tarjeta = document.getElementById('tarjeta');
        
        html2canvas(tarjeta).then((canvas) => {
            const imagen = canvas.toDataURL("image/png");
            const enlace = document.createElement("a");
            enlace.href = imagen;
            enlace.download = "TARJETA_DEL_PACIENTE.png";
            enlace.click();
        });
    }
    // Lógica para mostrar el modal de detalles de consulta si mascota_id está en la URL
    document.addEventListener('DOMContentLoaded', () => {
        const urlParams = new URLSearchParams(window.location.search);
        const currentMascotaId = urlParams.get('mascota_id');
        
        <?php if ($mascota_detalles && $mascota_id > 0): ?>
            // Si hay detalles de mascota y un ID de mascota en la URL, abrimos el modal de detalles
            const mascotaDataFromPHP = <?php echo json_encode($mascota_detalles); ?>;
            openDetallesConsultaModal(mascotaDataFromPHP);
        <?php endif; ?>
    });
    // Modal para editar paciente principal
    if (openEditPacienteModalBtn) {
        openEditPacienteModalBtn.addEventListener('click', () => {
            editPacientePrincipalModalOverlay.classList.add('active');
        });
    }
    if (closeEditPacientePrincipalModalBtn) {
        closeEditPacientePrincipalModalBtn.addEventListener('click', () => {
            editPacientePrincipalModalOverlay.classList.remove('active');
        });
    }
</script>

</body>
</html>
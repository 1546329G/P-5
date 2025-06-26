<?php
// agregar-consulta.php (Ahora maneja AGREGAR, EDITAR y ELIMINAR consultas)

// ======================================================
// 0. Configuración de Depuración PHP (solo para desarrollo, no en producción)
// ======================================================
// Habilitar la visualización de errores en el navegador (PELIGROSO EN PRODUCCIÓN)
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);

// Configurar dónde se guardarán los logs de error si no están ya configurados en php.ini
// Puedes especificar una ruta absoluta si quieres un archivo de log dedicado:
// ini_set('error_log', '/ruta/absoluta/a/tu/logs/php_errors.log');
// En Hostinger, a menudo los logs se escriben automáticamente en public_html/error_log


// Asegurarse de que solo se procesen solicitudes POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    error_log("agregar-consulta.php: Acceso no POST. Redirigiendo a index.php.");
    header("Location: index.php"); // Redirigir a una página principal si no es POST
    exit();
}

// ======================================================
// 1. Configuración de la Base de Datos (Tu configuración de Hostinger)
// ======================================================
$dbHost = "srv805.hstgr.io";
$dbUser = "u666383048_clinica";
$dbPass = "9~o0jY:Xw";
$dbName = "u666383048_clinica";
$dbPort = 3306;

// Establecer conexión con la base de datos
$conn = new mysqli($dbHost, $dbUser, $dbPass, $dbName, $dbPort);
if ($conn->connect_error) {
    $errorMessage = "Error de conexión a la base de datos: " . $conn->connect_error;
    error_log("agregar-consulta.php: " . $errorMessage); // Loguear error de conexión
    
    // Si la conexión falla y es una solicitud de eliminación (AJAX), responder con JSON
    if (isset($_POST['action']) && $_POST['action'] === 'delete_consulta') {
        header('Content-Type: application/json');
        http_response_code(500); // Internal Server Error
        echo json_encode(['success' => false, 'message' => $errorMessage]);
        exit();
    } else {
        // Redirigir siempre a detalle-propietario.php con el ID si está disponible
        $redirectId = isset($_POST['propietario_id']) ? intval($_POST['propietario_id']) : 0;
        header("Location: detalle-propietario.php?id=" . urlencode($redirectId) . "&error_mascota=" . urlencode($errorMessage));
        exit();
    }
}

error_log("agregar-consulta.php: Conexión a la base de datos exitosa."); // Loguear conexión exitosa

// ======================================================
// 2. Lógica UNIFICADA para AGREGAR, EDITAR y ELIMINAR Consulta
// ======================================================

// Determinar el tipo de acción a realizar
$action = isset($_POST['action']) ? htmlspecialchars(trim($_POST['action'])) : '';
// 'mascota_form_action_type' es para las acciones de agregar/editar desde el formulario modal de consulta
$action_type = isset($_POST['mascota_form_action_type']) ? htmlspecialchars(trim($_POST['mascota_form_action_type'])) : '';

error_log("agregar-consulta.php: action=" . $action . ", action_type=" . $action_type); // Loguear tipo de acción

// --- Lógica para ELIMINAR Consulta (AJAX) ---
if ($action === 'delete_consulta') { 
    error_log("agregar-consulta.php: Iniciando lógica de eliminación de consulta.");
    header('Content-Type: application/json'); // Responder siempre con JSON para eliminaciones AJAX
    $consulta_id_to_delete = isset($_POST['consulta_id']) ? intval($_POST['consulta_id']) : 0; // Se espera 'consulta_id' desde JS

    if ($consulta_id_to_delete <= 0) {
        error_log("agregar-consulta.php: ID de consulta inválido para eliminación: " . $consulta_id_to_delete);
        echo json_encode(['success' => false, 'message' => 'ID de consulta no proporcionado o inválido para la eliminación.']);
        $conn->close();
        exit();
    }

    try {
        $conn->begin_transaction(); // Iniciar una transacción

        $query = "DELETE FROM consultas_medicas WHERE id = ?";
        $stmt = $conn->prepare($query);

        if ($stmt === false) {
            error_log("DEBUG ELIMINAR: Error al preparar la eliminación: " . $conn->error);
            throw new Exception("Error al preparar la eliminación de consulta: " . $conn->error);
        }

        $stmt->bind_param("i", $consulta_id_to_delete);
        error_log("agregar-consulta.php: Preparada eliminación de consulta ID: " . $consulta_id_to_delete);

        if ($stmt->execute()) {
            if ($stmt->affected_rows > 0) {
                $conn->commit(); // Confirmar la transacción
                error_log("agregar-consulta.php: Consulta ID " . $consulta_id_to_delete . " eliminada correctamente.");
                echo json_encode(['success' => true, 'message' => 'Consulta eliminada correctamente.']);
            } else {
                $conn->rollback(); // Revertir si no se afectó ninguna fila (ID no encontrado)
                error_log("agregar-consulta.php: No se encontró la consulta ID " . $consulta_id_to_delete . " para eliminar.");
                echo json_encode(['success' => false, 'message' => 'No se encontró la consulta con el ID especificado o ya fue eliminada.']);
            }
        } else {
            error_log("DEBUG ELIMINAR: Error al ejecutar la eliminación: " . $stmt->error);
            $conn->rollback(); // Revertir la transacción en caso de error de ejecución
            throw new Exception("Error al ejecutar la eliminación de consulta: " . $stmt->error);
        }

        $stmt->close();

    } catch (Exception $e) {
        $conn->rollback(); // Revertir cualquier transacción
        error_log("DEBUG ELIMINAR CATCH: Error en eliminación de consulta: " . $e->getMessage());
        http_response_code(500); // Internal Server Error
        echo json_encode(['success' => false, 'message' => 'Ocurrió un error en el servidor al eliminar la consulta: ' . $e->getMessage()]);
    }
    $conn->close();
    exit(); // Es crucial salir aquí después de responder a la solicitud AJAX de eliminación
}

// --- Lógica para AGREGAR y EDITAR Consulta ---
// Esta sección se ejecutará solo si no fue una solicitud de eliminación y si action_type está presente
if (!empty($action_type)) {
    error_log("agregar-consulta.php: Iniciando lógica de AGREGAR/EDITAR consulta.");
    
    // Obtener y Validar ID del Propietario (ahora paciente_id)
    $paciente_id = isset($_POST['propietario_id']) ? intval($_POST['propietario_id']) : 0;

    if ($paciente_id === 0) {
        $errorMessage = "ID de paciente no proporcionado o inválido para la operación de consulta (agregar/editar).";
        error_log("agregar-consulta.php: " . $errorMessage);
        header("Location: detalle-propietario.php?error_mascota=" . urlencode($errorMessage)); // Mensaje genérico para mascotas, se puede cambiar
        exit();
    }

    // Sanear y obtener los datos comunes de la consulta
    $nombre_consulta = isset($_POST['nombre_consulta']) ? htmlspecialchars(trim($_POST['nombre_consulta'])) : '';
    $diagnostico_breve = isset($_POST['diagnostico_breve']) ? htmlspecialchars(trim($_POST['diagnostico_breve'])) : '';
    $sexo = isset($_POST['sexo']) ? htmlspecialchars(trim($_POST['sexo'])) : ''; // **VERIFICAR SI 'sexo' es una columna en 'consultas_medicas'**
    $especialidad = isset($_POST['especialidad']) ? htmlspecialchars(trim($_POST['especialidad'])) : '';
    $fecha_consulta = isset($_POST['fecha_consulta']) ? htmlspecialchars(trim($_POST['fecha_consulta'])) : '';
    $diagnostico_detallado = isset($_POST['diagnostico_detallado']) ? htmlspecialchars(trim($_POST['diagnostico_detallado'])) : '';

    error_log("agregar-consulta.php: Datos recibidos para consulta - paciente_id: " . $paciente_id . 
              ", nombre_consulta: " . $nombre_consulta . ", diagnostico_breve: " . $diagnostico_breve .
              ", sexo: " . $sexo . ", especialidad: " . $especialidad . 
              ", fecha_consulta: " . $fecha_consulta . ", diagnostico_detallado: " . $diagnostico_detallado);

    // Validaciones básicas de campos requeridos
    if (empty($nombre_consulta) || empty($diagnostico_breve) || empty($especialidad) || empty($fecha_consulta) || empty($diagnostico_detallado)) {
        $errorMessage = "Por favor, complete los campos requeridos (nombre, diagnóstico breve, especialidad, fecha de consulta y diagnóstico detallado) para la consulta.";
        error_log("agregar-consulta.php: Error de validación - " . $errorMessage);
        header("Location: detalle-propietario.php?id=" . urlencode($paciente_id) . "&error_mascota=" . urlencode($errorMessage));
        exit();
    }

    try {
        $conn->begin_transaction(); // Iniciar una transacción

        // Lógica para EDITAR CONSULTA EXISTENTE
        if ($action_type === 'edit_mascota') { // Asumiendo que tu JS envía este valor para la edición
            $id_consulta = isset($_POST['consulta_id_edit']) ? intval($_POST['consulta_id_edit']) : 0; // ID de la consulta a editar

            // DEBUG: Ver el ID de la consulta a editar
            error_log("DEBUG EDIT: ID de consulta a editar recibido: " . $id_consulta);

            if ($id_consulta === 0) {
                throw new Exception("ID de consulta inválido para la edición.");
            }

            // La consulta SQL para actualizar la consulta médica
            $query = "UPDATE consultas_medicas SET 
                                 nombre_consulta = ?, 
                                 diagnostico_breve = ?, 
                                 sexo = ?,          
                                 especialidad = ?, 
                                 fecha_consulta = ?, 
                                 diagnostico_detallado = ? 
                              WHERE id = ? AND paciente_id = ?"; // 8 placeholders: 6 para SET, 2 para WHERE
            $stmt = $conn->prepare($query);

            if ($stmt === false) {
                // Si la preparación falla, se lanza una excepción con el error de MySQL
                error_log("DEBUG EDIT ERROR: Falló prepare() para UPDATE: " . $conn->error); // Log del error del prepare
                throw new Exception("Error al preparar la actualización de consulta: " . $conn->error);
            }
            
            // Vincular parámetros para la actualización: 6 strings (ssssss) y 2 integers (ii)
            // IMPORTANTE: Si 'sexo' no existe en tu tabla 'consultas_medicas', quítalo de la query y del bind_param
            error_log("DEBUG EDIT: bind_param con tipos ssssssii."); // Log del tipo de bind_param
            error_log("DEBUG EDIT: Valores a bind_param (UPDATE): " . 
                "nombre_consulta='" . $nombre_consulta . "', " .
                "diagnostico_breve='" . $diagnostico_breve . "', " .
                "sexo='" . $sexo . "', " .
                "especialidad='" . $especialidad . "', " .
                "fecha_consulta='" . $fecha_consulta . "', " .
                "diagnostico_detallado='" . $diagnostico_detallado . "', " .
                "id_consulta=" . $id_consulta . ", " .
                "paciente_id=" . $paciente_id
            );
            $stmt->bind_param("ssssssii", // <-- CORRECCIÓN CLAVE: 6 's' y 2 'i'
                $nombre_consulta, 
                $diagnostico_breve, 
                $sexo, // Este campo debe existir en tu tabla consultas_medicas y ser de tipo STRING
                $especialidad, 
                $fecha_consulta, 
                $diagnostico_detallado, 
                $id_consulta,      // El ID de la consulta a actualizar (integer)
                $paciente_id       // El ID del paciente principal al que pertenece la consulta (integer)
            );
            error_log("agregar-consulta.php: Preparada actualización para consulta ID: " . $id_consulta);

        // Lógica para AGREGAR NUEVA CONSULTA
        } elseif ($action_type === 'agregar_mascota') { // Asumiendo que tu JS envía este valor para agregar
            $query = "INSERT INTO consultas_medicas (nombre_consulta, 
                                                     diagnostico_breve, sexo, especialidad, fecha_consulta, paciente_id, diagnostico_detallado) VALUES (?, ?, ?, ?, ?, ?, ?)"; // 7 placeholders
            $stmt = $conn->prepare($query);
            if ($stmt === false) {
                error_log("DEBUG AGREGAR ERROR: Falló prepare() para INSERT: " . $conn->error); // Log del error del prepare
                throw new Exception("Error al preparar la inserción de consulta: " . $conn->error);
            }
            // Vincular parámetros para la inserción: 6 strings (ssssss) y 1 integer (i)
            error_log("DEBUG AGREGAR: bind_param con tipos sssssis."); // Log del tipo de bind_param
            error_log("DEBUG AGREGAR: Valores a bind_param (INSERT): " . 
                "nombre_consulta='" . $nombre_consulta . "', " .
                "diagnostico_breve='" . $diagnostico_breve . "', " .
                "sexo='" . $sexo . "', " .
                "especialidad='" . $especialidad . "', " .
                "fecha_consulta='" . $fecha_consulta . "', " .
                "paciente_id=" . $paciente_id . ", " .
                "diagnostico_detallado='" . $diagnostico_detallado . "'"
            );
            $stmt->bind_param("sssssis", // Correcto para la inserción: 6 's' y 1 'i' (paciente_id)
                $nombre_consulta, 
                $diagnostico_breve, 
                $sexo, 
                $especialidad, 
                $fecha_consulta, 
                $paciente_id, 
                $diagnostico_detallado
            );
            error_log("agregar-consulta.php: Preparada inserción para paciente ID: " . $paciente_id);

        } else {
            // Si action_type no es ni 'edit_mascota' ni 'agregar_mascota'
            throw new Exception("Acción de formulario de consulta no válida.");
        }

        // Ejecutar la sentencia preparada (tanto para INSERT como para UPDATE)
        if ($stmt->execute()) {
            $conn->commit(); // Confirmar la transacción
            $success_message = ($action_type === 'agregar_mascota') ? "Nueva consulta registrada correctamente." : "Consulta actualizada correctamente.";
            error_log("agregar-consulta.php: " . $success_message . " Redirigiendo a detalle-propietario.php?id=" . $paciente_id);
            // Redirige de vuelta a la página de detalles del paciente con un mensaje de éxito
            header("Location: detalle-propietario.php?id=" . urlencode($paciente_id) . "&success_mascota=" . urlencode($success_message));
            exit();
        } else {
            // Obtener el error específico de la ejecución
            error_log("DEBUG EXECUTE ERROR: Falló execute() para la operación de consulta: " . $stmt->error); 
            $conn->rollback(); // Revertir la transacción en caso de error de ejecución
            throw new Exception("Error al ejecutar la operación en la consulta: " . $stmt->error);
        }
        $stmt->close(); // Cerrar la sentencia preparada

    } catch (Exception $e) {
        $conn->rollback(); // Asegurarse de revertir cualquier transacción si una excepción ocurre
        error_log("DEBUG CATCH GLOBAL: Error en procesamiento de consulta (agregar/editar): " . $e->getMessage()); // Loguear el error para depuración
        // Redirige de vuelta a la página de detalles del paciente con un mensaje de error
        header("Location: detalle-propietario.php?id=" . urlencode($paciente_id) . "&error_mascota=" . urlencode($e->getMessage()));
        exit();
    }
}

// ======================================================
// 4. Lógica para EDITAR CLIENTE PRINCIPAL
// Esta sección no debería estar aquí según tu HTML.
// Ya la movimos a `detalle-propietario.php`.
// La dejo comentada como recordatorio.
// ======================================================
if (isset($_POST['editar_paciente_principal']) && $_POST['editar_paciente_principal'] == '1') {
    error_log("AVISO: La lógica para editar el paciente principal (action 'editar_paciente_principal') NO DEBERÍA ESTAR en `agregar-consulta.php`. Según tu HTML (formEditarPacientePrincipal), se procesa en `detalle-propietario.php`. Se omite el procesamiento aquí para evitar duplicidad o errores.");
    // Aquí no se procesa la edición del paciente principal.
    // Esta parte del código es solo un log de aviso.
}

$conn->close(); // Cerrar la conexión a la base de datos al final del script
error_log("agregar-consulta.php: Script finalizado. Conexión cerrada.");
?>
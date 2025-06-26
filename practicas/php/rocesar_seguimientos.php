<?php
// procesar_seguimientos.php

// ======================================================
// 0. Configuración de Depuración PHP (solo para desarrollo)
// ======================================================
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);
// ini_set('error_log', '/ruta/absoluta/a/tu/logs/php_errors.log'); // Opcional, si quieres un log específico

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
    error_log("procesar_seguimientos.php: Error de conexión a la base de datos: " . $conn->connect_error);
    header('Content-Type: application/json');
    http_response_code(500); // Internal Server Error
    echo json_encode(['success' => false, 'message' => 'Error de conexión a la base de datos.']);
    exit();
}

// ======================================================
// 2. Lógica para OBTENER Seguimientos (Solicitud GET)
// ======================================================
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    error_log("procesar_seguimientos.php: Petición GET recibida para obtener seguimientos.");
    
    if (isset($_GET['consulta_id'])) {
        $consulta_id = intval($_GET['consulta_id']); // Sanear el ID

        if ($consulta_id <= 0) {
            error_log("procesar_seguimientos.php: GET - ID de consulta inválido: " . $consulta_id);
            header('Content-Type: application/json');
            http_response_code(400); // Bad Request
            echo json_encode(['success' => false, 'message' => 'ID de consulta no proporcionado o inválido.']);
            $conn->close();
            exit();
        }

        try {
            $query = "SELECT id, fecha_seguimiento, descripcion_seguimiento, doctor_seguimiento, observaciones_adicionales, fecha_creacion 
                      FROM seguimientos_consulta 
                      WHERE consulta_id = ? 
                      ORDER BY fecha_seguimiento ASC, fecha_creacion ASC";
            
            $stmt = $conn->prepare($query);

            if ($stmt === false) {
                error_log("procesar_seguimientos.php: GET - Error al preparar la consulta: " . $conn->error);
                throw new Exception("Error al preparar la consulta de seguimientos: " . $conn->error);
            }

            $stmt->bind_param("i", $consulta_id);
            
            if ($stmt->execute()) {
                $result = $stmt->get_result();
                $seguimientos = [];
                while ($row = $result->fetch_assoc()) {
                    $seguimientos[] = $row;
                }
                header('Content-Type: application/json');
                echo json_encode($seguimientos); // Devolver los seguimientos como JSON
            } else {
                error_log("procesar_seguimientos.php: GET - Error al ejecutar la consulta: " . $stmt->error);
                throw new Exception("Error al ejecutar la consulta de seguimientos: " . $stmt->error);
            }

            $stmt->close();

        } catch (Exception $e) {
            error_log("procesar_seguimientos.php: GET - Error en el servidor: " . $e->getMessage());
            header('Content-Type: application/json');
            http_response_code(500); // Internal Server Error
            echo json_encode(['success' => false, 'message' => 'Error en el servidor al obtener seguimientos: ' . $e->getMessage()]);
        }
    } else {
        error_log("procesar_seguimientos.php: GET - Parámetro 'consulta_id' no proporcionado.");
        header('Content-Type: application/json');
        http_response_code(400); // Bad Request
        echo json_encode(['success' => false, 'message' => 'Parámetro "consulta_id" es requerido.']);
    }
    $conn->close();
    exit(); // Terminar el script después de procesar la solicitud GET
}

// ======================================================
// 3. Lógica para AGREGAR Seguimientos (Solicitud POST)
// ======================================================
elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    error_log("procesar_seguimientos.php: Petición POST recibida para agregar seguimiento.");

    // Sanear y obtener los datos del POST
    $consulta_id = isset($_POST['consulta_id_seguimiento']) ? intval($_POST['consulta_id_seguimiento']) : 0;
    $fecha_seguimiento = isset($_POST['fecha_seguimiento']) ? htmlspecialchars(trim($_POST['fecha_seguimiento'])) : '';
    $descripcion_seguimiento = isset($_POST['descripcion_seguimiento']) ? htmlspecialchars(trim($_POST['descripcion_seguimiento'])) : '';
    $doctor_seguimiento = isset($_POST['doctor_seguimiento']) ? htmlspecialchars(trim($_POST['doctor_seguimiento'])) : NULL; // Puede ser NULL

    error_log("procesar_seguimientos.php: POST - Datos recibidos: consulta_id=" . $consulta_id . 
              ", fecha=" . $fecha_seguimiento . ", desc=" . $descripcion_seguimiento . 
              ", doctor=" . ($doctor_seguimiento ?? 'NULL'));

    // Validaciones básicas
    if ($consulta_id <= 0 || empty($fecha_seguimiento) || empty($descripcion_seguimiento)) {
        error_log("procesar_seguimientos.php: POST - Datos incompletos o inválidos.");
        header('Content-Type: application/json');
        http_response_code(400); // Bad Request
        echo json_encode(['success' => false, 'message' => 'Datos incompletos o inválidos para el seguimiento.']);
        $conn->close();
        exit();
    }

    try {
        $conn->begin_transaction(); // Iniciar transacción

        $query = "INSERT INTO seguimientos_consulta (consulta_id, fecha_seguimiento, descripcion_seguimiento, doctor_seguimiento) 
                  VALUES (?, ?, ?, ?)";
        
        $stmt = $conn->prepare($query);

        if ($stmt === false) {
            error_log("procesar_seguimientos.php: POST - Error al preparar la inserción: " . $conn->error);
            throw new Exception("Error al preparar el registro de seguimiento: " . $conn->error);
        }

        // Vincular parámetros: i (integer), s (string), s (string), s (string o null)
        $stmt->bind_param("isss", $consulta_id, $fecha_seguimiento, $descripcion_seguimiento, $doctor_seguimiento);
        
        if ($stmt->execute()) {
            $conn->commit(); // Confirmar la transacción
            error_log("procesar_seguimientos.php: POST - Seguimiento registrado correctamente para consulta_id: " . $consulta_id);
            header('Content-Type: application/json');
            echo json_encode(['success' => true, 'message' => 'Seguimiento registrado correctamente.']);
        } else {
            $conn->rollback(); // Revertir la transacción
            error_log("procesar_seguimientos.php: POST - Error al ejecutar la inserción: " . $stmt->error);
            throw new Exception("Error al ejecutar el registro de seguimiento: " . $stmt->error);
        }

        $stmt->close();

    } catch (Exception $e) {
        $conn->rollback(); // Asegurarse de revertir cualquier transacción
        error_log("procesar_seguimientos.php: POST - Error en el servidor: " . $e->getMessage());
        header('Content-Type: application/json');
        http_response_code(500); // Internal Server Error
        echo json_encode(['success' => false, 'message' => 'Error en el servidor al registrar el seguimiento: ' . $e->getMessage()]);
    }
    $conn->close();
    exit(); // Terminar el script después de procesar la solicitud POST
}

// ======================================================
// 4. Manejo de Solicitudes no Reconocidas
// ======================================================
else {
    error_log("procesar_seguimientos.php: Acceso inválido. Se esperaba GET o POST.");
    header('Content-Type: application/json');
    http_response_code(405); // Method Not Allowed
    echo json_encode(['success' => false, 'message' => 'Método de solicitud no permitido.']);
    $conn->close();
    exit();
}
?>
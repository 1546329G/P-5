<?php
header('Content-Type: application/json');

$dbHost = "srv805.hstgr.io"; // Host proporcionado por Hostinger
$dbUser = "u666383048_clinica"; // Usuario de la base de datos
$dbPass = "9~o0jY:Xw"; // Contraseña del usuario
$dbName = "u666383048_clinica"; // Nombre de la base de datos
$dbPort = 3306; // Puerto de la base de datos (generalmente 3306 para MySQL)

// Establecer conexión con la base de datos
// Se incluye el puerto como un parámetro adicional en mysqli
$conn = new mysqli($dbHost, $dbUser, $dbPass, $dbName, $dbPort);
if ($conn->connect_error) {
    echo json_encode(['error' => 'Error de conexión a la base de datos.']);
    exit;
}

// Validar método POST
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    echo json_encode(['error' => 'Método no permitido.']);
    exit;
}

// Capturar datos del formulario
$cliente_id = intval($_POST['cliente_id'] ?? 0);
$mascota_id = intval($_POST['mascota_id'] ?? 0);
$fecha_visita = $_POST['fecha_visita'] ?? '';
$descripcion = $conn->real_escape_string($_POST['descripcion'] ?? '');

// Validar campos obligatorios
if (!$cliente_id || !$mascota_id || !$fecha_visita || !$descripcion) {
    echo json_encode(['error' => 'Todos los campos son obligatorios.']);
    exit;
}

// Insertar en la tabla historial_visitas
$sql = "INSERT INTO historial_visitas (cliente_id, mascota_id, fecha_visita, descripcion) 
        VALUES ($cliente_id, $mascota_id, '$fecha_visita', '$descripcion')";

if ($conn->query($sql) === TRUE) {
    echo json_encode(['success' => 'Descripción agregada correctamente.']);
} else {
    echo json_encode(['error' => 'Error al guardar en la base de datos: ' . $conn->error]);
}

$conn->close();
?>

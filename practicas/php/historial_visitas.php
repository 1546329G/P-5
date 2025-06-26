<?php
header('Content-Type: application/json');

$dbHost = "srv805.hstgr.io";
$dbUser = "u666383048_clinica";
$dbPass = "9~o0jY:Xw";
$dbName = "u666383048_clinica";
$dbPort = 3306;

$conn = new mysqli($dbHost, $dbUser, $dbPass, $dbName, $dbPort);
if ($conn->connect_error) {
    echo json_encode(['error' => 'Error de conexión a la base de datos.']);
    exit;
}
$conn->set_charset("utf8mb4");

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    echo json_encode(['error' => 'Método no permitido.']);
    exit;
}

$paciente_id = intval($_POST['paciente_id'] ?? 0);     // Cambiado de cliente_id
$consulta_id = intval($_POST['consulta_id'] ?? 0);     // Cambiado de mascota_id
$fecha_visita = $_POST['fecha_visita'] ?? '';
$descripcion = $conn->real_escape_string($_POST['descripcion'] ?? '');

if (!$paciente_id || !$consulta_id || !$fecha_visita || !$descripcion) {
    echo json_encode(['error' => 'Todos los campos son obligatorios.']);
    exit;
}

// Insertar en la tabla 'visitas_detalle' con las nuevas columnas
$sql = "INSERT INTO visitas_detalle (paciente_id, consulta_id, fecha_visita, descripcion) 
        VALUES ($paciente_id, $consulta_id, '$fecha_visita', '$descripcion')";

if ($conn->query($sql) === TRUE) {
    echo json_encode(['success' => 'Visita agregada correctamente.']);
} else {
    echo json_encode(['error' => 'Error al guardar la visita en la base de datos: ' . $conn->error]);
}

$conn->close();
?>
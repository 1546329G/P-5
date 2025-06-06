<?php
header('Content-Type: application/json');

// Configuración de la base de datos
$host = 'localhost';
$user = 'root';
$password = '';
$database = 'veterinaria';

// Conectar a la base de datos
$conn = new mysqli($host, $user, $password, $database);

// Verificar conexión
if ($conn->connect_error) {
    die(json_encode(["error" => "Error de conexión: " . $conn->connect_error]));
}

// Obtener datos de cliente y mascota
$cliente_id = isset($_GET['cliente_id']) ? intval($_GET['cliente_id']) : null;
$mascota_id = isset($_GET['mascota_id']) ? intval($_GET['mascota_id']) : null;

if (!$cliente_id) {
    echo json_encode(["error" => "Cliente ID es obligatorio."]);
    exit;
}

// Construir la consulta
$sql = "SELECT fecha_visita, descripcion FROM historial_visitas WHERE cliente_id = $cliente_id";
if ($mascota_id) {
    $sql .= " AND mascota_id = $mascota_id";
}

$result = $conn->query($sql);
$historial = [];

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $historial[] = $row;
    }
    echo json_encode($historial);
} else {
    echo json_encode(["error" => "No se encontraron visitas para este cliente."]);
}

// Cerrar conexión
$conn->close();
?>

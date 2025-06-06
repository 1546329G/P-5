<?php
include 'conexion.php'; // Conexión a la base de datos

// Verificar que sea una solicitud POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Obtener datos del formulario
    $cliente_id = intval($_POST['cliente_id'] ?? 0);
    $mascota_id = intval($_POST['mascota_id'] ?? 0);
    $fecha_visita = $_POST['fecha_visita'] ?? '';
    $descripcion = $conn->real_escape_string($_POST['descripcion'] ?? '');

    while ($mascota = $resultado->fetch_assoc()) {
        echo "<li>";
        echo "Nombre: " . $mascota['nombre'] . " (" . $mascota['tipo'] . ")";
        echo " <button class='btn-mas-info' data-mascota-id='" . $mascota['id'] . "'>Más información</button>";
        echo "</li>";
    }

    // Validar campos
    if (!$cliente_id || !$mascota_id || !$fecha_visita || !$descripcion) {
        die("Todos los campos son obligatorios.");
    }

    // Guardar en el historial
    $query = "INSERT INTO historial_visitas (cliente_id, mascota_id, fecha_visita, descripcion)
              VALUES ($cliente_id, $mascota_id, '$fecha_visita', '$descripcion')";
    if ($conn->query($query) === TRUE) {
        echo "Descripción agregada correctamente.";
    } else {
        echo "Error al guardar en la base de datos: " . $conn->error;
    }
} else {
    die("Método no permitido.");
}
?>

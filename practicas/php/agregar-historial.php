<?php
// procesar_historial_visitas.php (Nombre sugerido, o el que uses para este script)

include 'conexion.php'; // Conexión a la base de datos

// Verificar que sea una solicitud POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Obtener datos del formulario
    // Asegúrate de que 'cliente_id' en el POST corresponda al ID del paciente
    $paciente_id = intval($_POST['cliente_id'] ?? 0); 
    // Asegúrate de que 'mascota_id' en el POST corresponda al ID de la consulta médica
    $consulta_id = intval($_POST['mascota_id'] ?? 0); 
    $fecha_visita = $_POST['fecha_visita'] ?? '';
    // La descripción aquí se interpreta como las 'notas_seguimiento' o 'descripcion'
    // en la tabla 'visitas_detalle'
    $descripcion_visita = $_POST['descripcion'] ?? ''; // Renombrado para claridad

    if (!$paciente_id || !$consulta_id || !$fecha_visita || empty($descripcion_visita)) {
        // Redirigir o dar un mensaje de error más específico
        // die("Todos los campos son obligatorios.");
        // Se recomienda una redirección a la página de origen con un mensaje de error
        $redirect_url = "detalle-propietario.php?id=" . urlencode($paciente_id) . "&error_visita=" . urlencode("Todos los campos son obligatorios para registrar la visita.");
        header("Location: " . $redirect_url);
        exit();
    }

    // Guardar en el historial (ahora 'visitas_detalle')
    // Usando prepared statements para seguridad
    $query = "INSERT INTO visitas_detalle (paciente_id, consulta_id, fecha_visita, descripcion)
              VALUES (?, ?, ?, ?)";
    
    $stmt = $conn->prepare($query);

    if ($stmt === false) {
        // Error en la preparación de la consulta
        error_log("Error al preparar INSERT en visitas_detalle: " . $conn->error);
        $redirect_url = "detalle-propietario.php?id=" . urlencode($paciente_id) . "&error_visita=" . urlencode("Error interno del servidor al preparar el registro de la visita.");
        header("Location: " . $redirect_url);
        exit();
    }

    // 'i' para integer (paciente_id, consulta_id), 's' para string (fecha_visita, descripcion)
    $stmt->bind_param("iiss", $paciente_id, $consulta_id, $fecha_visita, $descripcion_visita);

    if ($stmt->execute()) {
        // Éxito al insertar
        $success_message = "Descripción de visita agregada correctamente.";
        $redirect_url = "detalle-propietario.php?id=" . urlencode($paciente_id) . "&success_visita=" . urlencode($success_message);
        header("Location: " . $redirect_url);
        exit();
    } else {
        // Error en la ejecución
        error_log("Error al guardar visita en la base de datos: " . $stmt->error);
        $error_message = "Error al guardar en la base de datos: " . $stmt->error;
        $redirect_url = "detalle-propietario.php?id=" . urlencode($paciente_id) . "&error_visita=" . urlencode($error_message);
        header("Location: " . $redirect_url);
        exit();
    }

    $stmt->close();

} else {
    // Si no es una solicitud POST
    // die("Método no permitido.");
    header("Location: index.php"); // Redirigir a una página principal
    exit();
}

// Cierre de conexión (si se llega aquí, lo cual no debería pasar si se usa exit() correctamente)
if ($conn) {
    $conn->close();
}
?>
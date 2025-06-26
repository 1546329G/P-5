<?php
$dbHost = "srv805.hstgr.io"; // Host proporcionado por Hostinger
$dbUser = "u666383048_clinica"; // Usuario de la base de datos
$dbPass = "9~o0jY:Xw"; // Contraseña del usuario
$dbName = "u666383048_clinica"; // Nombre de la base de datos
$dbPort = 3306; // Puerto de la base de datos (generalmente 3306 para MySQL)

// Establecer conexión con la base de datos
// Se incluye el puerto como un parámetro adicional en mysqli
$conn = new mysqli($dbHost, $dbUser, $dbPass, $dbName, $dbPort);
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Establecer el charset a UTF-8 para asegurar la correcta codificación de caracteres especiales
$conn->set_charset("utf8mb4");

// Cambiado de 'clientes' a 'pacientes'
$sql = "SELECT id, nombre, direccion, telefono, dni, fechaSeguimientoInicio FROM pacientes";
$result = $conn->query($sql);

if ($result && $result->num_rows > 0) {
    // Nombre del archivo CSV
    // Cambiado de 'clientes_veterinaria' a 'pacientes_clinica' para mayor coherencia
    $filename = "pacientes_clinica_" . date('Y-m-d') . ".csv";

    header('Content-Type: text/csv; charset=UTF-8');
    header('Content-Disposition: attachment;filename="' . $filename . '"');
    header('Pragma: no-cache');
    header('Expires: 0');

    $output = fopen('php://output', 'w');

    // Nombres de las columnas para el encabezado del CSV
    // Cambiado 'Nombre' (asumiendo que 'propietario' era el anterior) a 'Nombre del Paciente'
    // 'Fecha de Registro' sigue siendo 'fechaSeguimientoInicio' en la DB 'pacientes'
    fputcsv($output, ['ID', 'Nombre del Paciente', 'Dirección', 'Teléfono', 'DNI', 'Fecha de Seguimiento Inicio']);

    while ($row = $result->fetch_assoc()) {
        fputcsv($output, [
            $row['id'],
            $row['nombre'], // Cambiado de $row['propietario'] a $row['nombre']
            $row['direccion'],
            $row['telefono'],
            $row['dni'],
            $row['fechaSeguimientoInicio']
        ]);
    }

    fclose($output);
    exit;
} else {
    echo "No se encontraron pacientes."; // Mensaje actualizado
}
$conn->close();
?>
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

$sql = "SELECT * FROM clientes";
$result = $conn->query($sql);

if ($result && $result->num_rows > 0) {
    // Nombre del archivo CSV
    $filename = "clientes_veterinaria_" . date('Y-m-d') . ".csv";

    header('Content-Type: text/csv; charset=UTF-8');
    header('Content-Disposition: attachment;filename="' . $filename . '"');
    header('Pragma: no-cache');
    header('Expires: 0');

    $output = fopen('php://output', 'w');

    fputcsv($output, ['ID', 'Nombre', 'Dirección', 'Teléfono', 'DNI', 'Fecha de Registro']);

    while ($row = $result->fetch_assoc()) {
        fputcsv($output, [
            $row['id'],
            $row['propietario'],
            $row['direccion'],
            $row['telefono'],
            $row['dni'],
            $row['fechaSeguimientoInicio']
        ]);
    }

    fclose($output);
    exit;
} else {
    echo "No se encontraron clientes.";
}
$conn->close();
?>

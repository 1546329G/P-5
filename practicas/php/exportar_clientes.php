<?php
$conn = new mysqli("localhost", "root", "", "veterinaria");

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

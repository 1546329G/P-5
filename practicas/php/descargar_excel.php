<?php
$conn = new mysqli("localhost", "root", "", "veterinaria");
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

$sql = "SELECT * FROM clientes WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $filename = "detalles_propietario_" . $id . ".csv";

    header('Content-Type: text/csv');
    header('Content-Disposition: attachment;filename="' . $filename . '"');

    $output = fopen('php://output', 'w');

    fputcsv($output, ['Campo', 'Valor']);

    // Escribir los datos del propietario
    foreach ($row as $key => $value) {
        fputcsv($output, [ucfirst($key), $value]);
    }

    $sql_mascotas = "SELECT * FROM mascotas WHERE propietario_id = ?";
    $stmt_mascotas = $conn->prepare($sql_mascotas);
    $stmt_mascotas->bind_param("i", $id);
    $stmt_mascotas->execute();
    $result_mascotas = $stmt_mascotas->get_result();

    // Verificar si hay mascotas y agregar al CSV
    if ($result_mascotas->num_rows > 0) {
        fputcsv($output, ['']); // Línea en blanco entre propietario y mascotas
        fputcsv($output, ['Mascotas:']);
        fputcsv($output, ['Nombre', 'Especie', 'Raza', 'Fecha de Nacimiento']);
        
        while ($mascota = $result_mascotas->fetch_assoc()) {
            fputcsv($output, [
                $mascota['nombre'],
                $mascota['especie'],
                $mascota['raza'],
                date("d/m/Y", strtotime($mascota['fechaNacimiento']))
            ]);
        }
    } else {
        fputcsv($output, ['No hay mascotas registradas.']);
    }
    fputcsv($output, ['']); 
    fputcsv($output, ['Descripción del Propietario:']);
    $descripcion = isset($row['descripcion']) ? $row['descripcion'] : 'No se proporcionó descripción.';
    fputcsv($output, [$descripcion]);

    fclose($output);
} else {
    echo "No se encontró el propietario.";
}

$stmt->close();
$conn->close();
?>




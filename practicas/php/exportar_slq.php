<?php
$conn = new mysqli("localhost", "root", "", "veterinaria");

if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

$nombre_archivo = 'veterinaria.sql';

$salida_sql = '';
$resultado = $conn->query("SHOW TABLES");

while ($tabla = $resultado->fetch_row()) {
    $nombre_tabla = $tabla[0];

    $salida_sql .= "DROP TABLE IF EXISTS $nombre_tabla;\n";
    $resultado_crear_tabla = $conn->query("SHOW CREATE TABLE $nombre_tabla");
    $crear_tabla = $resultado_crear_tabla->fetch_row();
    $salida_sql .= $crear_tabla[1] . ";\n\n";

    $resultado_datos = $conn->query("SELECT * FROM $nombre_tabla");

    while ($fila = $resultado_datos->fetch_assoc()) {
        $valores = array_map(array($conn, 'real_escape_string'), $fila);
        $salida_sql .= "INSERT INTO $nombre_tabla (" . implode(",", array_keys($fila)) . ") VALUES ('" . implode("','", $valores) . "');\n";
    }

    $salida_sql .= "\n";
}

file_put_contents($nombre_archivo, $salida_sql);

$conn->close();

// Forzar la descarga del archivo SQL
header('Content-Type: application/sql');
header('Content-Disposition: attachment; filename="' . $nombre_archivo . '"');
header('Content-Length: ' . filesize($nombre_archivo));
readfile($nombre_archivo);

// Eliminar el archivo después de la descarga
unlink($nombre_archivo);
exit;
?>

<?php
$dbHost = "srv805.hstgr.io";
$dbUser = "u666383048_clinica";
$dbPass = "9~o0jY:Xw";
$dbName = "u666383048_clinica";
$dbPort = 3306;

$conn = new mysqli($dbHost, $dbUser, $dbPass, $dbName, $dbPort);
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

$nombre_archivo = 'respaldo_clinica_' . date('Y-m-d_H-i-s') . '.sql'; // Nombre más descriptivo y único
$salida_sql = '';

$resultado = $conn->query("SHOW TABLES");

while ($tabla = $resultado->fetch_row()) {
    $nombre_tabla = $tabla[0];

    $salida_sql .= "DROP TABLE IF EXISTS `$nombre_tabla`;\n"; // Asegura comillas en nombre de tabla
    
    $resultado_crear_tabla = $conn->query("SHOW CREATE TABLE `$nombre_tabla`"); // Asegura comillas
    $crear_tabla = $resultado_crear_tabla->fetch_row();
    $salida_sql .= $crear_tabla[1] . ";\n\n";

    $resultado_datos = $conn->query("SELECT * FROM `$nombre_tabla`"); // Asegura comillas

    if ($resultado_datos->num_rows > 0) {
        while ($fila = $resultado_datos->fetch_assoc()) {
            $columnas = array_map(function($col) use ($conn) {
                return "`" . $conn->real_escape_string($col) . "`"; // Asegura comillas en nombres de columna
            }, array_keys($fila));
            
            $valores = array_map(function($val) use ($conn) {
                return "'" . $conn->real_escape_string($val) . "'"; // Asegura comillas en valores y escapa
            }, $fila);
            
            $salida_sql .= "INSERT INTO `$nombre_tabla` (" . implode(",", $columnas) . ") VALUES (" . implode(",", $valores) . ");\n";
        }
    }
    $salida_sql .= "\n";
}

file_put_contents($nombre_archivo, $salida_sql);

$conn->close();

header('Content-Type: application/sql');
header('Content-Disposition: attachment; filename="' . $nombre_archivo . '"');
header('Content-Length: ' . filesize($nombre_archivo));
readfile($nombre_archivo);

unlink($nombre_archivo);
exit;
?>
<?php
$dbHost = "srv805.hstgr.io"; // Host proporcionado por Hostinger
$dbUser = "u666383048_clinica"; // Usuario de la base de datos
$dbPass = "9~o0jY:Xw"; // Contraseña del usuario
$dbName = "u666383048_clinica"; // Nombre de la base de datos
$dbPort = 3306; // Puerto de la base de datos (generalmente 3306 para MySQL)

// Establecer conexión con la base de datos
// Se incluye el puerto como un parámetro adicional en mysqli
$conn = new mysqli($dbHost, $dbUser, $dbPass, $dbName, $dbPort);

$backupFile = "C:\\xampp\\htdocs\\practicas\\php\\veterinaria.sql";

// Ruta completa a mysqldump (asegúrate de que esta ruta sea correcta en tu instalación de XAMPP)
$mysqldumpPath = "C:\\xampp\\mysql\\bin\\mysqldump";
$lastBackupFile = "C:\\xampp\\htdocs\\practicas\\php\\ultimo_respaldo.txt";

// Intervalo de tiempo entre respaldos (en segundos, 86400 segundos = 24 horas)
$intervalo = 86400;

// Verificar si se debe generar un respaldo (si han pasado más de 24 horas desde el último)
if (!file_exists($lastBackupFile) || time() - file_get_contents($lastBackupFile) > $intervalo) {
    file_put_contents($lastBackupFile, time());

    $command = "\"$mysqldumpPath\" -h $host -u $user $db > \"$backupFile\"";
    exec($command, $output, $result);
    if ($result === 0) {
        echo "El respaldo se ha generado correctamente en: $backupFile";
    } else {
        echo "Hubo un error al generar el respaldo. Código de error: $result<br>";
        echo "Salida del comando: " . implode("\n", $output);
    }
    echo "<br>Comando ejecutado: $command";
} else {
    echo "No se ha generado un respaldo porque aún no ha pasado el intervalo de 24 horas.";
}
?>

<?php
// Iniciar sesión SIEMPRE al principio
session_start();

// Validar que la solicitud sea POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // 1. Configuración de la base de datos de Hostinger
    $dbHost = "srv805.hstgr.io"; // Host proporcionado por Hostinger
    $dbUser = "u666383048_clinica"; // Usuario de la base de datos
    $dbPass = "9~o0jY:Xw"; // Contraseña del usuario
    $dbName = "u666383048_clinica"; // Nombre de la base de datos
    $dbPort = 3306; // Puerto de la base de datos (generalmente 3306 para MySQL)

    // 2. Establecer conexión con la base de datos usando mysqli
    $conn = new mysqli($dbHost, $dbUser, $dbPass, $dbName, $dbPort);

    // 3. Verificar si hubo un error en la conexión a la base de datos
    if ($conn->connect_error) {
        header("Location: ../index.html?error_db=1&msg=" . urlencode($conn->connect_error));
        exit();
    }

    // Establecer el charset a UTF-8 para evitar problemas con caracteres especiales
    $conn->set_charset("utf8");

    // 4. Recibir datos del formulario y sanitizarlos
    $user = $conn->real_escape_string($_POST['username']);
    $pass = $_POST['password'];

    // 5. Preparar la consulta SQL para buscar el usuario
    // BASADO EN TU 'select * from usuarios;', TUS COLUMNAS SON 'username' y 'password'.
    // Si NO tienes una columna 'id_usuario', DEBES QUITARLA de aquí.
    // Lo más seguro es empezar con lo que SÍ sabes que existe:
    $sql = "SELECT username, password FROM usuarios WHERE username = ?";
    // Si *estás absolutamente seguro* de que tienes una columna 'id_usuario' y su nombre exacto, úsala:
    // $sql = "SELECT id_usuario, username, password FROM usuarios WHERE username = ?";
    
    $stmt = $conn->prepare($sql);

    // *******************************************************************
    // ****** ESTE ES EL PASO CRÍTICO PARA LA DEPURACIÓN AHORA **********
    // ****** Capturamos y mostramos el error de MySQL si prepare() falla ******
    // *******************************************************************
    if ($stmt === false) {
        // Prepare() falló. Muestra el error de MySQL
        // Esto debería darte el mensaje exacto de por qué la consulta es inválida
        die("Error al preparar la consulta: " . $conn->error . "<br>Consulta SQL intentada: " . htmlspecialchars($sql));
        // O si prefieres redirigir:
        // header("Location: ../index.html?error_sql=1&msg=" . urlencode($conn->error . " | Query: " . $sql));
        // exit();
    }
    // *******************************************************************

    // 6. Vincular el parámetro del nombre de usuario
    $stmt->bind_param("s", $user);

    // 7. Ejecutar la consulta
    $stmt->execute();

    // 8. Obtener el resultado de la consulta
    $result = $stmt->get_result();

    // 9. Verificar si se encontró un usuario con ese nombre
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        // Obtener la contraseña en texto plano de la DB
        $password_from_db = $row['password']; 

        // 10. Verificar la contraseña ingresada con la contraseña de la base de datos (TEXTO PLANO)
        if ($pass === $password_from_db) {
            // Contraseña correcta: Iniciar sesión
            // Guardar el ID del usuario si la columna 'id_usuario' existe y fue seleccionada
            if (isset($row['id_usuario'])) { // Verificar si 'id_usuario' existe en el resultado
                $_SESSION['usuario_id'] = $row['id_usuario'];
            }
            $_SESSION['username'] = $row['username'];
            $_SESSION['autenticado'] = true;

            // Redirigir a la página principal después de un login exitoso
            header("Location: ventanas.php");
            exit();
        } else {
            // Contraseña incorrecta
            header("Location: ../index.html?error=1"); // Error: Usuario o contraseña incorrectos
            exit();
        }
    } else {
        // Usuario no encontrado
        header("Location: ../index.html?error=1"); // Error: Usuario o contraseña incorrectos
        exit();
    }

    // 11. Cerrar la sentencia y la conexión a la base de datos
    $stmt->close();
    $conn->close();

} else {
    // Si alguien intenta acceder a procesar_login.php directamente sin un POST
    header("Location: ../index.html");
    exit();
}
?>
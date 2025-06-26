<?php
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $dbHost = "srv805.hstgr.io";
    $dbUser = "u666383048_clinica";
    $dbPass = "9~o0jY:Xw";
    $dbName = "u666383048_clinica";
    $dbPort = 3306;

    $conn = new mysqli($dbHost, $dbUser, $dbPass, $dbName, $dbPort);

    if ($conn->connect_error) {
        header("Location: ../index.html?error_db=1&msg=" . urlencode("Error de conexión a la base de datos."));
        exit();
    }

    $conn->set_charset("utf8");

    $user = $conn->real_escape_string($_POST['username']);
    $pass = $_POST['password'];

    $sql = "SELECT username, password FROM usuarios WHERE username = ?";
    // Si la columna 'id_usuario' existe y la necesitas, usa esta línea en su lugar:
    // $sql = "SELECT id_usuario, username, password FROM usuarios WHERE username = ?";
    
    $stmt = $conn->prepare($sql);

    if ($stmt === false) {
        header("Location: ../index.html?error_sql=1&msg=" . urlencode("Error al preparar la consulta."));
        exit();
    }

    $stmt->bind_param("s", $user);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $password_from_db = $row['password']; 

        if ($pass === $password_from_db) {
            if (isset($row['id_usuario'])) {
                $_SESSION['usuario_id'] = $row['id_usuario'];
            }
            $_SESSION['username'] = $row['username'];
            $_SESSION['autenticado'] = true;

            header("Location: ventanas.php");
            exit();
        } else {
            header("Location: ../index.html?error=1"); // Usuario o contraseña incorrectos
            exit();
        }
    } else {
        header("Location: ../index.html?error=1"); // Usuario o contraseña incorrectos
        exit();
    }

    $stmt->close();
    $conn->close();

} else {
    header("Location: ../index.html");
    exit();
}
?>
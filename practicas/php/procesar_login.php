<?php
// Iniciar sesión
session_start();

// Conexión a la base de datos (debes adaptar estos valores a tu configuración)
$servername = "localhost";
$username = "tu_usuario";
$password = "tu_password";
$dbname = "tu_base_de_datos";

// Recibir datos del formulario
$user = $_POST['username'];
$pass = $_POST['password'];

// Para efectos de demostración, voy a incluir un usuario "hardcodeado"
// En el sistema real,  consultara a la base de datos
$usuario_demo = "admin";
$password_demo = "12345";

// Verificar si es el usuario de prueba
if ($user === $usuario_demo && $pass === $password_demo) {
    // Usuario válido - guardar información en la sesión
    $_SESSION['usuario'] = $user;
    $_SESSION['autenticado'] = true;
    
    // Redirigir a la página principal
    header("Location: ventanas.php");
    exit();
} 
// Si quieres conectar con la base de datos, puedes usar este código en su lugar:
/*
try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Consulta preparada para evitar inyecciones SQL
    $stmt = $conn->prepare("SELECT * FROM usuarios WHERE username = :username");
    $stmt->bindParam(':username', $user);
    $stmt->execute();
    
    // Verificar si existe el usuario
    if ($stmt->rowCount() > 0) {
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
        // Verificar contraseña (asumiendo que está almacenada con password_hash)
        if (password_verify($pass, $row['password'])) {
            // Usuario válido
            $_SESSION['usuario'] = $user;
            $_SESSION['autenticado'] = true;
            
            // Redirigir a la página principal
            header("Location: ventanas.php");
            exit();
        }
    }
} catch(PDOException $e) {
    // Error de conexión
    echo "Error: " . $e->getMessage();
}
*/

// Si no es válido, redireccionar con mensaje de error
header("Location: ../index.html?error=1");
exit();
?>
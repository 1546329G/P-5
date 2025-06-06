<?php
// Iniciar sesión
session_start();

// Conexión a la base de datos
$servername = "localhost";
$db_username = "root";  // Renombrada para evitar confusión
$db_password = "";      // Renombrada para evitar confusión
$dbname = "veterinaria";

// Recibir datos del formulario
$user = $_POST['username'];
$pass = $_POST['password'];

try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $db_username, $db_password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Consulta preparada para evitar inyecciones SQL
    $stmt = $conn->prepare("SELECT * FROM usuarios WHERE username = :username");
    $stmt->bindParam(':username', $user);
    $stmt->execute();
    
    // Verificar si existe el usuario
    if ($stmt->rowCount() > 0) {
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
        // MODIFICACIÓN IMPORTANTE: Verificación directa si la contraseña está en texto plano
        if ($pass === $row['password']) {
            // Usuario válido
            $_SESSION['username'] = $user;
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

// Si no es válido, redireccionar con mensaje de error
header("Location: ../index.html?error=1");
exit();
?>
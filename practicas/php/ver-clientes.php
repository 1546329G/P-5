<?php
// Conexión a la base de datos
$dbHost = "srv805.hstgr.io";
$dbUser = "u666383048_clinica";
$dbPass = "9~o0jY:Xw";
$dbName = "u666383048_clinica";
$dbPort = 3306;

$conn = new mysqli($dbHost, $dbUser, $dbPass, $dbName, $dbPort);
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}
$conn->set_charset("utf8mb4");

$mensajeEliminacion = "";

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['id'])) {
    $id = intval($_POST['id']);

    $conn->begin_transaction(); // Iniciar transacción

    // Inicializar statements a null para el finally
    $stmt_visitas = null;
    $stmt_mascotas = null;
    $stmt_paciente = null;

    try {
        // 1. Eliminar registros de visitas_detalle asociados al paciente
        // ASEGÚRATE de que la tabla 'visitas_detalle' exista y tenga la columna 'paciente_id'
        $stmt_visitas = $conn->prepare("DELETE FROM visitas_detalle WHERE paciente_id = ?");
        if ($stmt_visitas === false) {
            throw new Exception("Error al preparar DELETE visitas_detalle: " . $conn->error);
        }
        $stmt_visitas->bind_param("i", $id);
        $stmt_visitas->execute();

        // 2. Eliminar mascotas asociadas a este paciente (propietario)
        // ASEGÚRATE de que la tabla 'mascotas' exista y tenga la columna 'propietario_id'
        // que se relaciona con 'pacientes.id'
        

        // 3. Eliminar al paciente de la tabla principal
        // ASEGÚRATE de que la tabla 'pacientes' exista y tenga la columna 'id'
        $stmt_paciente = $conn->prepare("DELETE FROM pacientes WHERE id = ?");
        if ($stmt_paciente === false) {
            throw new Exception("Error al preparar DELETE pacientes: " . $conn->error);
        }
        $stmt_paciente->bind_param("i", $id);
        if ($stmt_paciente->execute()) {
            if ($stmt_paciente->affected_rows > 0) {
                $mensajeEliminacion = "Paciente eliminado con éxito.";
            } else {
                // Esto podría ocurrir si el ID no existe, pero la consulta fue válida
                $mensajeEliminacion = "Advertencia: El paciente con ID $id no fue encontrado.";
            }
        } else {
            throw new Exception("No se pudo eliminar al paciente: " . $stmt_paciente->error);
        }

        $conn->commit(); // Confirmar la transacción si todo salió bien
    } catch (Exception $e) {
        $conn->rollback(); // Revertir la transacción si hubo un error
        $mensajeEliminacion = "Error en la eliminación: " . $e->getMessage();
    } finally {
        // Asegúrate de que los statements se cierren incluso si hay una excepción
        // Solo cierra si el statement fue exitosamente preparado (no es null y es un objeto)
        if ($stmt_visitas !== null) $stmt_visitas->close();
    
        if ($stmt_paciente !== null) $stmt_paciente->close();
    }
}

// Consultar la lista de pacientes
$sql = "SELECT id, nombre FROM pacientes";
$result = $conn->query($sql);

if (!$result) {
    die("Error en la consulta de pacientes: " . $conn->error); // Más específico
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lista de Pacientes</title>
    <link rel="stylesheet" type="text/css" href="../css2/verclientes.css">
    <link rel="icon" href="../img/favicon2.ico" type="image/x-icon">
    <script>
        function eliminarPaciente(id) {
            if (confirm("¿Seguro que deseas eliminar este paciente y todos sus datos asociados (mascotas y visitas)? Esta acción es irreversible.")) {
                document.getElementById("form-eliminar-" + id).submit();
            }
        }
    </script>
</head>
<body>

<div class="propietario-lista">
    <h2>Lista de Pacientes</h2>

    <?php if ($mensajeEliminacion): ?>
        <div class="mensaje-eliminacion">
            <?php echo htmlspecialchars($mensajeEliminacion); ?>
        </div>
    <?php endif; ?>

    <table class="lista">
        <tr>
            <th>ID</th>
            <th>Nombre del Paciente</th>
            <th>Acciones</th>
        </tr>

        <?php
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo "<tr id='fila-" . $row["id"] . "'>";
                echo "<td>" . htmlspecialchars($row["id"]) . "</td>";
                echo "<td>" . htmlspecialchars($row["nombre"]) . "</td>";
                echo "<td>
                        <a href='detalle-propietario.php?id=" . $row["id"] . "' class='btn-info'>MÁS INFORMACIÓN</a>
                        <form id='form-eliminar-" . $row["id"] . "' action='' method='POST' style='display:inline-block; margin-left: 10px;'>
                            <input type='hidden' name='id' value='" . $row["id"] . "'>
                            <button type='button' onclick='eliminarPaciente(" . $row["id"] . ")' class='btn-danger'>Eliminar</button>
                        </form>
                      </td>";
                echo "</tr>";
            }
        } else {
            echo "<tr><td colspan='3'>No hay pacientes registrados</td></tr>";
        }
        ?>
    </table>
</div>

<div class="center">
    <form method="POST" action="exportar_pacientes.php" style="display: inline-block; margin-right: 10px;">
        <button type="submit" class="btn btn-primary">Descargar datos de los pacientes</button>
    </form>
    <form action="exportar_sql.php" method="POST" style="display: inline-block;">
        <button type="submit" class="btn btn-secondary">Exportar Base de Datos</button>
    </form>
</div>

<div class="volver">
    <a href="ventanas.php" class="btn-volver">Volver a la Página Principal</a>
</div>

</body>
</html>

<?php
// Asegurarse de cerrar la conexión a la DB al final del script
if ($conn) {
    $conn->close();
}
?>
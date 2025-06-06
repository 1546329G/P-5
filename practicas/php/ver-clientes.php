<?php
// Conexión a la base de datos
$conn = new mysqli("localhost", "root", "", "veterinaria");

if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

$mensajeEliminacion = "";

// Verificar si se recibió una solicitud para eliminar un cliente
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['id'])) {
    $id = intval($_POST['id']);

    // Iniciar transacción
    $conn->begin_transaction();

    try {
        // Eliminar historial de visitas
        $stmt_historial = $conn->prepare("DELETE FROM historial_visitas WHERE cliente_id = ?");
        $stmt_historial->bind_param("i", $id);
        $stmt_historial->execute();

        // Eliminar mascotas
        $stmt_mascotas = $conn->prepare("DELETE FROM mascotas WHERE propietario_id = ?");
        $stmt_mascotas->bind_param("i", $id);
        $stmt_mascotas->execute();

        // Eliminar cliente
        $stmt_cliente = $conn->prepare("DELETE FROM clientes WHERE id = ?");
        $stmt_cliente->bind_param("i", $id);
        if ($stmt_cliente->execute()) {
            $mensajeEliminacion = "Cliente eliminado con éxito.";
        } else {
            throw new Exception("No se pudo eliminar al cliente.");
        }

        $conn->commit();
    } catch (Exception $e) {
        $conn->rollback();
        $mensajeEliminacion = "Error: " . $e->getMessage();
    }

    $stmt_historial->close();
    $stmt_mascotas->close();
    $stmt_cliente->close();
}

// Consultar la lista de clientes (mostrando nombre en lugar de doctor)
$sql = "SELECT id, nombre FROM clientes";
$result = $conn->query($sql);

if (!$result) {
    die("Error en la consulta: " . $conn->error);
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
        function eliminarCliente(id) {
            if (confirm("¿Seguro que deseas eliminar este cliente?")) {
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
                        <form id='form-eliminar-" . $row["id"] . "' action='' method='POST' style='display:inline-block;'>
                            <input type='hidden' name='id' value='" . $row["id"] . "'>
                            <button type='button' onclick='eliminarCliente(" . $row["id"] . ")'>Eliminar</button>
                        </form>
                      </td>";
                echo "</tr>";
            }
        } else {
            echo "<tr><td colspan='3'>No hay clientes registrados</td></tr>";
        }
        ?>
    </table>
</div>

<div class="center">
<form method="POST" action="exportar_clientes.php">
    <button type="submit">Descargar datos de los clientes</button>
</form>
<form action="exportar_sql.php" method="POST">
    <button type="submit">Exportar Base de Datos</button>
</form>
</div>

<div class="volver">
    <a href="ventanas.php" class="btn-volver">Volver a la Página Principal</a>
</div>

</body>
</html>

<?php
// Cerrar conexión
$conn->close();
?>
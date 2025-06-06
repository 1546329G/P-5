<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro en Clinica</title>
    <link rel="stylesheet" href="../css/conexion.css"> <!-- Archivo CSS -->
</head>
<body>
    <div class="container">
        <?php
        // Conexión a la base de datos
        $conn = new mysqli("localhost", "root", "", "veterinaria");

        if ($conn->connect_error) {
            die("<div class='message error'>Conexión fallida: " . $conn->connect_error . "</div>");
        }

        $conn->set_charset("utf8mb4");

        // Recibimos los datos de la solicitud POST
        $propietario = $_POST['doctor'];
        $direccion = $_POST['direccion'];
        $telefono = $_POST['telefono'];
        $paciente = $_POST['nombre'];
        $fechaNacimiento = $_POST['fechaNacimiento'];
        $dni = $_POST['dni'];
        $especie = $_POST['nacionalidad'];
        $raza = $_POST['diagnostico'];
        $sexo = $_POST['sexo'];
        $color = $_POST['especialidad'];
        $fechaSeguimientoInicio = $_POST['fechaSeguimientoInicio'];
        $descripcion = $_POST['descripcion'];

        if ($sexo != 'hombre' && $sexo != 'mujer') {
            echo "<div class='message error'>Valor de sexo no válido. Solo se permiten 'masculino' o 'femenino'.</div>";
            exit;
        }

        // Verificar si el cliente ya existe por el DNI
        $stmt_verificar = $conn->prepare("SELECT id FROM clientes WHERE dni = ?");
        $stmt_verificar->bind_param("s", $dni);
        $stmt_verificar->execute();
        $resultado_cliente = $stmt_verificar->get_result();

        if ($resultado_cliente->num_rows > 0) {
            $cliente_existente = $resultado_cliente->fetch_assoc();
            $cliente_id = $cliente_existente['id'];

            // Insertar la mascota para el cliente existente
            $stmt_mascota = $conn->prepare("INSERT INTO mascotas (nombre, nacionalidad, diagnostico, sexo, especialidad, fechaNacimiento, propietario_id)
                                            VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt_mascota->bind_param("ssssssi", $paciente, $especie, $raza, $sexo, $color, $fechaNacimiento, $cliente_id);

            if ($stmt_mascota->execute()) {
                $mascota_id = $conn->insert_id; // Obtener el ID de la mascota insertada
                echo "<div class='message success'>Mascota registrada con éxito para el cliente existente.</div>";

                // Insertar la descripción en la tabla historial_visitas
                $fecha_visita = date("Y-m-d"); // O usa la fecha que desees
                $stmt_historial = $conn->prepare("INSERT INTO historial_visitas (cliente_id, mascota_id, descripcion, fecha_visita)
                                                  VALUES (?, ?, ?, ?)");
                $stmt_historial->bind_param("iiss", $cliente_id, $mascota_id, $descripcion, $fecha_visita);

                if ($stmt_historial->execute()) {
                    echo "<div class='message success'>Descripción registrada correctamente en el historial de visitas.</div>";
                } else {
                    echo "<div class='message error'>Error al registrar la descripción: " . $stmt_historial->error . "</div>";
                }
            } else {
                echo "<div class='message error'>Error al registrar la mascota: " . $stmt_mascota->error . "</div>";
            }
        } else {
            // Registrar un nuevo cliente y su mascota
            $stmt_cliente = $conn->prepare("INSERT INTO clientes (doctor, direccion, telefono, dni, nombre, fechaNacimiento, nacionalidad, diagnostico, sexo, especialidad, fechaSeguimientoInicio)
                                            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt_cliente->bind_param("sssssssssss", $propietario, $direccion, $telefono, $dni, $paciente, $fechaNacimiento, $especie, $raza, $sexo, $color, $fechaSeguimientoInicio);

            if ($stmt_cliente->execute()) {
                $cliente_id = $conn->insert_id;

                // Insertar la mascota para el nuevo cliente
                $stmt_mascota = $conn->prepare("INSERT INTO mascotas (nombre, nacionalidad, diagnostico, sexo, especialidad, fechaNacimiento, propietario_id)
                                                VALUES (?, ?, ?, ?, ?, ?, ?)");
                $stmt_mascota->bind_param("ssssssi", $paciente, $especie, $raza, $sexo, $color, $fechaNacimiento, $cliente_id);

                if ($stmt_mascota->execute()) {
                    $mascota_id = $conn->insert_id; // Obtener el ID de la mascota insertada
                    echo "<div class='message success'>Cliente y mascota registrados con éxito.</div>";

                    // Insertar la descripción en historial_visitas
                    $fecha_visita = date("Y-m-d"); // O usa la fecha que desees
                    $stmt_historial = $conn->prepare("INSERT INTO historial_visitas (cliente_id, mascota_id, descripcion, fecha_visita)
                                                      VALUES (?, ?, ?, ?)");
                    $stmt_historial->bind_param("iiss", $cliente_id, $mascota_id, $descripcion, $fecha_visita);

                    if ($stmt_historial->execute()) {
                        echo "<div class='message success'>Descripción registrada correctamente en el historial de visitas.</div>";
                    } else {
                        echo "<div class='message error'>Error al registrar la descripción: " . $stmt_historial->error . "</div>";
                    }
                } else {
                    echo "<div class='message error'>Error al registrar la mascota: " . $stmt_mascota->error . "</div>";
                }
            } else {
                echo "<div class='message error'>Error al registrar el cliente: " . $stmt_cliente->error . "</div>";
            }
        }

        // Respaldo de la base de datos
        $host = "localhost";
        $user = "root";
        $pass = "";
        $db = "veterinaria";
        $backupFile = "C:\\xampp\\htdocs\\practicas\\php\\veterinaria.sql";
        $mysqldumpPath = "C:\\xampp\\mysql\\bin\\mysqldump";

        $command = "\"$mysqldumpPath\" -h $host -u $user $db > \"$backupFile\"";
        exec($command, $output, $result);

        if ($result === 0) {
            echo "<div class='message success'>El respaldo se ha generado correctamente en:<br> <code>$backupFile</code></div>";
        } else {
            echo "<div class='message error'>Hubo un error al generar el respaldo. Código de error: $result<br>Salida del comando: " . implode("\n", $output) . "</div>";
        }
        ?>
       <div class="container">
        <div class="volver">
            <a href="ventanas.php" class="btn-volver">Volver a la Página Principal</a>
        </div>
    </div>
    </div>
</body>
</html>

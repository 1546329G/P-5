<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro en Clínica</title>
    <link rel="stylesheet" href="../css/conexion.css">
</head>
<body>
    <div class="container">
        <?php
        $dbHost = "srv805.hstgr.io";
        $dbUser = "u666383048_clinica";
        $dbPass = "9~o0jY:Xw";
        $dbName = "u666383048_clinica";
        $dbPort = 3306;

        $conn = new mysqli($dbHost, $dbUser, $dbPass, $dbName, $dbPort);
        if ($conn->connect_error) {
            die("<div class='message error'>Conexión fallida: " . $conn->connect_error . "</div>");
        }

        $conn->set_charset("utf8mb4");

        // Recibimos los datos de la solicitud POST
        // Datos del paciente principal (antes propietario)
        $nombre_paciente_principal = $_POST['propietario'] ?? '';
        $direccion = $_POST['direccion'] ?? '';
        $telefono = $_POST['telefono'] ?? '';
        $dni = $_POST['dni'] ?? '';
        $fechaNacimiento_paciente_principal = $_POST['fechaNacimiento'] ?? ''; // Asumiendo que es la fecha de nacimiento del propietario/paciente principal
        // Nuevo campo para paciente principal: nacionalidad. Si no viene del form, puedes omitirlo o darle un default
        $nacionalidad_paciente_principal = $_POST['nacionalidad'] ?? 'Desconocida'; 

        // Datos de la primera consulta (antes mascota)
        $nombre_consulta = $_POST['paciente'] ?? ''; // Nombre del paciente/consulta
        $diagnostico_breve = $_POST['especie'] ?? ''; // Usando 'especie' como diagnóstico breve inicial
        $sexo_consulta = $_POST['sexo'] ?? '';
        $especialidad = $_POST['raza'] ?? ''; // Usando 'raza' como especialidad inicial
        $fecha_consulta = $_POST['fechaNacimiento'] ?? ''; // Usando fechaNacimiento del form como fecha de la consulta
        $diagnostico_detallado = $_POST['descripcion'] ?? ''; // Descripción detallada de la primera consulta

        // Validación de sexo
        if ($sexo_consulta != 'macho' && $sexo_consulta != 'hembra') {
            echo "<div class='message error'>Valor de sexo no válido para la consulta. Solo se permiten 'macho' o 'hembra'.</div>";
            exit;
        }

        // Iniciar transacción
        $conn->begin_transaction();

        try {
            $paciente_id = 0; // Inicializar cliente_id

            // Verificar si el paciente principal ya existe por el DNI
            $stmt_verificar_paciente = $conn->prepare("SELECT id FROM pacientes WHERE dni = ?");
            if (!$stmt_verificar_paciente) {
                throw new Exception("Error al preparar verificación de paciente: " . $conn->error);
            }
            $stmt_verificar_paciente->bind_param("s", $dni);
            $stmt_verificar_paciente->execute();
            $resultado_paciente = $stmt_verificar_paciente->get_result();

            if ($resultado_paciente->num_rows > 0) {
                $paciente_existente = $resultado_paciente->fetch_assoc();
                $paciente_id = $paciente_existente['id'];
                echo "<div class='message info'>Paciente principal existente (ID: {$paciente_id}). Registrando nueva consulta.</div>";
                // Opcional: Actualizar datos del paciente principal si se modifican
                // $stmt_update_paciente = $conn->prepare("UPDATE pacientes SET nombre=?, direccion=?, telefono=?, fechaNacimiento=?, nacionalidad=? WHERE id=?");
                // $stmt_update_paciente->bind_param("sssssi", $nombre_paciente_principal, $direccion, $telefono, $fechaNacimiento_paciente_principal, $nacionalidad_paciente_principal, $paciente_id);
                // $stmt_update_paciente->execute();
                // $stmt_update_paciente->close();
            } else {
                // Insertar nuevo paciente principal
                $stmt_insert_paciente = $conn->prepare("INSERT INTO pacientes (nombre, direccion, telefono, dni, fechaNacimiento, nacionalidad) VALUES (?, ?, ?, ?, ?, ?)");
                if (!$stmt_insert_paciente) {
                    throw new Exception("Error al preparar inserción de paciente: " . $conn->error);
                }
                $stmt_insert_paciente->bind_param("ssssss", $nombre_paciente_principal, $direccion, $telefono, $dni, $fechaNacimiento_paciente_principal, $nacionalidad_paciente_principal);
                
                if ($stmt_insert_paciente->execute()) {
                    $paciente_id = $conn->insert_id;
                    echo "<div class='message success'>Nuevo paciente principal registrado con éxito (ID: {$paciente_id}).</div>";
                } else {
                    throw new Exception("Error al registrar el paciente principal: " . $stmt_insert_paciente->error);
                }
                $stmt_insert_paciente->close();
            }
            $stmt_verificar_paciente->close();

            // Insertar la primera consulta médica asociada al paciente principal
            $stmt_consulta = $conn->prepare("INSERT INTO consultas_medicas (paciente_id, nombre_consulta, diagnostico_breve, sexo, especialidad, fecha_consulta, diagnostico_detallado) VALUES (?, ?, ?, ?, ?, ?, ?)");
            if (!$stmt_consulta) {
                throw new Exception("Error al preparar inserción de consulta médica: " . $conn->error);
            }
            $stmt_consulta->bind_param("issssss", $paciente_id, $nombre_consulta, $diagnostico_breve, $sexo_consulta, $especialidad, $fecha_consulta, $diagnostico_detallado);

            if ($stmt_consulta->execute()) {
                $consulta_id = $conn->insert_id;
                echo "<div class='message success'>Primera consulta médica registrada con éxito (ID: {$consulta_id}).</div>";

                // Insertar la descripción inicial de la consulta en visitas_detalle
                $fecha_visita_detalle = date("Y-m-d"); // Fecha de la visita es hoy, o puedes usar $fecha_consulta si es lo mismo
                $stmt_visita_detalle = $conn->prepare("INSERT INTO visitas_detalle (paciente_id, consulta_id, descripcion, fecha_visita) VALUES (?, ?, ?, ?)");
                if (!$stmt_visita_detalle) {
                    throw new Exception("Error al preparar inserción de visitas_detalle: " . $conn->error);
                }
                $stmt_visita_detalle->bind_param("iiss", $paciente_id, $consulta_id, $diagnostico_detallado, $fecha_visita_detalle);

                if ($stmt_visita_detalle->execute()) {
                    echo "<div class='message success'>Descripción de la primera visita registrada correctamente.</div>";
                } else {
                    throw new Exception("Error al registrar la descripción de la primera visita: " . $stmt_visita_detalle->error);
                }
                $stmt_visita_detalle->close();
            } else {
                throw new Exception("Error al registrar la consulta médica: " . $stmt_consulta->error);
            }
            $stmt_consulta->close();

            $conn->commit(); // Confirmar la transacción si todo fue bien

        } catch (Exception $e) {
            $conn->rollback(); // Revertir la transacción si algo falló
            echo "<div class='message error'>Error en el proceso de registro: " . $e->getMessage() . "</div>";
            error_log("Error en registro de paciente/consulta: " . $e->getMessage()); // Para depuración en logs
        }

        $conn->close();
        ?>
        <div class="container">
            <div class="volver">
                <a href="ventanas.php" class="btn-volver">Volver a la Página Principal</a>
            </div>
        </div>
    </div>
</body>
</html>
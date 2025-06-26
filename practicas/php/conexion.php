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
        // --- Configuración de la conexión a la base de datos ---
        $dbHost = "srv805.hstgr.io";
        $dbUser = "u666383048_clinica";
        $dbPass = "9~o0jY:Xw";
        $dbName = "u666383048_clinica";
        $dbPort = 3306;

        $conn = new mysqli($dbHost, $dbUser, $dbPass, $dbName, $dbPort);

        // Verificar la conexión
        if ($conn->connect_error) {
            die("<div class='message error'>Conexión fallida: " . $conn->connect_error . "</div>");
        }
        $conn->set_charset("utf8mb4");

        // --- Recibir y sanear los datos de la solicitud POST ---
        // Datos para la tabla 'pacientes' (ex-clientes) - Propietario/Paciente Principal
        $nombre_paciente_principal = $_POST['doctor'] ?? ''; // Asumo 'doctor' es el nombre del paciente principal
        $direccion = $_POST['direccion'] ?? '';
        $telefono = $_POST['telefono'] ?? '';
        $dni = $_POST['dni'] ?? '';
        $fechaNacimiento_paciente_principal = $_POST['fechaNacimiento_propietario'] ?? ''; // Nueva variable para distinguir
        $nacionalidad_paciente_principal = $_POST['nacionalidad_propietario'] ?? ''; // Nueva variable para distinguir

        // Datos para la tabla 'consultas_medicas' (ex-mascotas) - Detalles de la consulta
        $nombre_consulta = $_POST['nombre'] ?? ''; // Nombre del paciente/consulta (e.g., "Max", "Consulta General")
        $diagnostico_breve = $_POST['diagnostico'] ?? ''; // Diagnóstico breve (antes 'raza' en tu mapeo anterior)
        $sexo_consulta = $_POST['sexo'] ?? ''; // Sexo de la mascota/paciente
        $especialidad_consulta = $_POST['especialidad'] ?? ''; // Especialidad (antes 'color' en tu mapeo)
        $fecha_consulta = $_POST['fechaSeguimientoInicio'] ?? ''; // Fecha de la consulta inicial
        $diagnostico_detallado = $_POST['descripcion'] ?? ''; // Diagnóstico detallado/descripción inicial

        // Validación de sexo
        if ($sexo_consulta != 'masculino' && $sexo_consulta != 'femenino') {
            echo "<div class='message error'>Valor de sexo no válido. Solo se permiten 'masculino' o 'femenino'.</div>";
            exit;
        }

        // Iniciar transacción
        $conn->begin_transaction();

        try {
            $paciente_id = 0;

            // 1. Verificar o insertar en la tabla 'pacientes' (el dueño/paciente principal)
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
                echo "<div class='message info'>Paciente principal existente (ID: {$paciente_id}).</div>";
                // Opcional: Actualizar datos del paciente principal si cambian
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

            // 2. Insertar en la tabla 'consultas_medicas' (los detalles de la consulta/mascota)
            $stmt_consulta = $conn->prepare("INSERT INTO consultas_medicas (paciente_id, nombre_consulta, diagnostico_breve, sexo, especialidad, fecha_consulta, diagnostico_detallado) VALUES (?, ?, ?, ?, ?, ?, ?)");
            if (!$stmt_consulta) {
                throw new Exception("Error al preparar inserción de consulta médica: " . $conn->error);
            }
            $stmt_consulta->bind_param("issssss",
                $paciente_id,
                $nombre_consulta,
                $diagnostico_breve,
                $sexo_consulta,
                $especialidad_consulta,
                $fecha_consulta,
                $diagnostico_detallado
            );

            if ($stmt_consulta->execute()) {
                $consulta_id = $conn->insert_id;
                echo "<div class='message success'>Consulta médica registrada con éxito (ID: {$consulta_id}).</div>";

                // 3. Insertar la primera entrada en 'visitas_detalle' (el historial de la primera visita)
                $fecha_visita_detalle = date("Y-m-d"); // O usar $fecha_consulta si es la misma fecha de la primera visita
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
            error_log("Error en registro de paciente/consulta: " . $e->getMessage()); // Para depuración en logs del servidor
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
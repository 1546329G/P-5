<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro en Clínica</title>
    <link rel="stylesheet" href="../css/conexion.css"> </head>
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
        $propietario = $_POST['doctor'] ?? '';
        $direccion = $_POST['direccion'] ?? '';
        $telefono = $_POST['telefono'] ?? '';
        $paciente = $_POST['nombre'] ?? ''; // Este es el nombre de la mascota
        $fechaNacimiento = $_POST['fechaNacimiento'] ?? ''; // Fecha de nacimiento de la mascota
        $dni = $_POST['dni'] ?? ''; // DNI del cliente
        $especie = $_POST['nacionalidad'] ?? ''; 
        $raza = $_POST['diagnostico'] ?? '';    
        $sexo = $_POST['sexo'] ?? '';
        $color = $_POST['especialidad'] ?? '';   // 'especialidad' se usa para color en mascotas
        $fechaSeguimientoInicio = $_POST['fechaSeguimientoInicio'] ?? '';
        $descripcion = $_POST['descripcion'] ?? ''; // Descripción del cliente (en tabla clientes)

     
        if ($sexo != 'masculino' && $sexo != 'femenino') {
            echo "<div class='message error'>Valor de sexo no válido. Solo se permiten 'masculino' o 'femenino'.</div>";
            exit; // Detiene la ejecución
        }

        // Verificar si el cliente ya existe por el DNI 
        $stmt_verificar = $conn->prepare("SELECT id FROM clientes WHERE dni = ?");
        if ($stmt_verificar === false) {
            die("<div class='message error'>Error al preparar la consulta de verificación de cliente: " . $conn->error . "</div>");
        }
        $bind_verificar_success = $stmt_verificar->bind_param("s", $dni);
        if ($bind_verificar_success === false) {
            die("<div class='message error'>Error en bind_param para verificación de cliente: " . $stmt_verificar->error . "</div>");
        }
        $stmt_verificar->execute();
        $resultado_cliente = $stmt_verificar->get_result();

        if ($resultado_cliente->num_rows > 0) {
            // --- Caso 1: El cliente ya existe ---
            $cliente_existente = $resultado_cliente->fetch_assoc();
            $cliente_id = $cliente_existente['id'];

            echo "<div class='message info'>Cliente existente detectado. Registrando solo la mascota.</div>";

            // Insertar la mascota para el cliente existente
            
            $stmt_mascota = $conn->prepare("INSERT INTO mascotas (nombre, nacionalidad, diagnostico, sexo, especialidad, fechaNacimiento, propietario_id)
                                             VALUES (?, ?, ?, ?, ?, ?, ?)");

            if ($stmt_mascota === false) {
                die("<div class='message error'>Error al preparar la consulta de mascota (cliente existente): " . $conn->error . "</div>");
            }

            // Asegurar que las variables sean del tipo correcto antes de enlazar
            $paciente_c = (string) $paciente;
            $especie_c = (string) $especie;
            $raza_c = (string) $raza;
            $sexo_c = (string) $sexo;
            $color_c = (string) $color;
            $fechaNacimiento_c = (string) $fechaNacimiento;
            $cliente_id_c = (int) $cliente_id; 

            $bind_mascota_success = $stmt_mascota->bind_param(
                "ssssssi", // 7 tipos: 6 strings, 1 int
                $paciente_c,
                $especie_c,
                $raza_c,
                $sexo_c,
                $color_c,
                $fechaNacimiento_c,
                $cliente_id_c
            );

            if ($bind_mascota_success === false) {
                die("<div class='message error'>Error en bind_param para mascota (cliente existente): " . $stmt_mascota->error . " (Código: " . $stmt_mascota->errno . ")</div>");
            }

            if ($stmt_mascota->execute()) {
                $mascota_id = $conn->insert_id; // Obtener el ID de la mascota insertada
                echo "<div class='message success'>Mascota registrada con éxito para el cliente existente.</div>";

                // Insertar en la tabla historial_visitas 
              
                $fecha_visita = date("Y-m-d"); 

                $stmt_historial = $conn->prepare("INSERT INTO historial_visitas (cliente_id, mascota_id, fecha_visita)
                                                     VALUES (?, ?, ?)");

                if ($stmt_historial === false) {
                    die("<div class='message error'>Error al preparar consulta de historial (cliente existente): " . $conn->error . "</div>");
                }

                $bind_historial_success = $stmt_historial->bind_param(
                    "iis", // 3 tipos: 2 int, 1 string
                    $cliente_id_c, 
                    $mascota_id,
                    $fecha_visita
                );

                if ($bind_historial_success === false) {
                    die("<div class='message error'>Error en bind_param para historial (cliente existente): " . $stmt_historial->error . " (Código: " . $stmt_historial->errno . ")</div>");
                }

                if ($stmt_historial->execute()) {
                    echo "<div class='message success'>Visita registrada correctamente en el historial.</div>";
                } else {
                    echo "<div class='message error'>Error al registrar la visita en historial: " . $stmt_historial->error . "</div>";
                }
            } else {
                echo "<div class='message error'>Error al registrar la mascota: " . $stmt_mascota->error . "</div>";
            }
        } else {
            // Caso 2: Registrar un nuevo cliente y su mascota 
            // 1. Insertar el nuevo cliente
          
            $stmt_cliente = $conn->prepare("INSERT INTO clientes (doctor, direccion, telefono, dni, nombre, fechaNacimiento, nacionalidad, diagnostico, sexo, especialidad, fechaSeguimientoInicio, descripcion )
                                             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

            if ($stmt_cliente === false) {
                die("<div class='message error'>Error al preparar la consulta de cliente: " . $conn->error . "</div>");
            }

            // Asegurar que las variables sean del tipo correcto antes de enlazar
            $propietario_c = (string) $propietario;
            $direccion_c = (string) $direccion;
            $telefono_c = (string) $telefono;
            $dni_c = (string) $dni;

            // Para el nombre del cliente, si el formulario no envía un campo 'nombre_cliente' separado
            // se usará el 'nombre' (que es el nombre de la mascota en tu formulario)
            $nombre_cliente_c = (string) $paciente; // Usar el nombre de la mascota como nombre del cliente por defecto

            $fechaNacimiento_cliente_c = (string) $fechaNacimiento; 
            $nacionalidad_cliente_c = (string) $especie; 
            $diagnostico_cliente_c = (string) $raza;     
            $sexo_cliente_c = (string) $sexo;
            $especialidad_cliente_c = (string) $color;   

            $fechaSeguimientoInicio_c = (string) $fechaSeguimientoInicio;
            $descripcion_c = (string) $descripcion; 

            $bind_cliente_success = $stmt_cliente->bind_param(
                "ssssssssssss", // 12 tipos 's'
                $propietario_c, $direccion_c, $telefono_c, $dni_c, $nombre_cliente_c, $fechaNacimiento_cliente_c,
                $nacionalidad_cliente_c, $diagnostico_cliente_c, $sexo_cliente_c, $especialidad_cliente_c, $fechaSeguimientoInicio_c, $descripcion_c
            );

            if ($bind_cliente_success === false) {
                die("<div class='message error'>Error en bind_param para cliente: " . $stmt_cliente->error . " (Código: " . $stmt_cliente->errno . ")</div>");
            }

            if ($stmt_cliente->execute()) {
                $cliente_id = $conn->insert_id; // Obtener el ID del cliente recién insertado
                echo "<div class='message success'>Cliente registrado con éxito.</div>";

               
                $stmt_mascota = $conn->prepare("INSERT INTO mascotas (nombre, nacionalidad, diagnostico, sexo, especialidad, fechaNacimiento, propietario_id )
                                             VALUES (?, ?, ?, ?, ?, ?, ?)");

                if ($stmt_mascota === false) {
                    die("<div class='message error'>Error al preparar la consulta de mascota (nuevo cliente): " . $conn->error . "</div>");
                }

                // Reutilizar variables casteadas (ya procesadas al inicio del script)
                $paciente_c = (string) $paciente; 
                $especie_c = (string) $especie;   
                $raza_c = (string) $raza;         
                $sexo_c = (string) $sexo;
                $color_c = (string) $color;      // Especialidad de la mascota
                $fechaNacimiento_c = (string) $fechaNacimiento;
                $cliente_id_c = (int) $cliente_id;

                $bind_mascota_success = $stmt_mascota->bind_param(
                    "ssssssi", // 7 tipos: 6 strings, 1 int
                    $paciente_c,
                    $especie_c,
                    $raza_c,
                    $sexo_c,
                    $color_c,
                    $fechaNacimiento_c,
                    $cliente_id_c
                );

                if ($bind_mascota_success === false) {
                    die("<div class='message error'>Error en bind_param para mascota (nuevo cliente): " . $stmt_mascota->error . " (Código: " . $stmt_mascota->errno . ")</div>");
                }

                if ($stmt_mascota->execute()) {
                    $mascota_id = $conn->insert_id; // Obtener el ID de la mascota recién insertada
                    echo "<div class='message success'>Mascota registrada con éxito para el nuevo cliente.</div>";

                    // 3. Insertar en la tabla historial_visitas
            
                    $fecha_visita = date("Y-m-d"); 

                    $stmt_historial = $conn->prepare("INSERT INTO historial_visitas (cliente_id, mascota_id, fecha_visita)
                                                         VALUES (?, ?, ?)");

                    if ($stmt_historial === false) {
                        die("<div class='message error'>Error al preparar consulta de historial (nuevo cliente): " . $conn->error . "</div>");
                    }

                    $bind_historial_success = $stmt_historial->bind_param(
                        "iis", // 3 tipos: 2 int, 1 string
                        $cliente_id_c,
                        $mascota_id,
                        $fecha_visita
                    );

                    if ($bind_historial_success === false) {
                        die("<div class='message error'>Error en bind_param para historial (nuevo cliente): " . $stmt_historial->error . " (Código: " . $stmt_historial->errno . ")</div>");
                    }

                    if ($stmt_historial->execute()) {
                        echo "<div class='message success'>Visita registrada correctamente en el historial.</div>";
                    } else {
                        echo "<div class='message error'>Error al registrar la visita en historial: " . $stmt_historial->error . "</div>";
                    }

                } else {
                    echo "<div class='message error'>Error al registrar la mascota (nuevo cliente): " . $stmt_mascota->error . "</div>";
                }

            } else { 
                echo "<div class='message error'>Error al registrar el cliente: " . $stmt_cliente->error . "</div>";
            }
        }

        // --- Cierre de sentencias y conexión a la BD ---
        // Cerrar las sentencias preparadas cuando ya no se necesitan
        if (isset($stmt_verificar)) $stmt_verificar->close();
        if (isset($stmt_cliente)) $stmt_cliente->close();
        if (isset($stmt_mascota)) $stmt_mascota->close();
        if (isset($stmt_historial)) $stmt_historial->close();
        $conn->close();



        // --- Respaldo de la base de datos ---
        // Asegúrate de que las credenciales para mysqldump sean correctas para tu entorno local (XAMPP)
        $host_backup = "localhost"; 
        $user_backup = "root";     
        $pass_backup = "";      
        $db_backup = "veterinaria";

        // Ruta completa a mysqldump y al archivo de respaldo
        $mysqldumpPath = "C:\\xampp\\mysql\\bin\\mysqldump";
        $backupFile = "C:\\xampp\\htdocs\\P-5\\practicas\\php\\veterinaria.sql";

        // Construir el comando de respaldo
        // Usar comillas dobles alrededor de las rutas para manejar espacios y comillas en la contraseña
        $command = "\"$mysqldumpPath\" -h \"$host_backup\" -u \"$user_backup\"";
        if (!empty($pass_backup)) { // Solo añadir la contraseña si no está vacía
            $command .= " -p\"$pass_backup\""; // -p seguido DIRECTAMENTE de la contraseña
        }
        $command .= " \"$db_backup\" > \"$backupFile\" 2>&1"; // Redirigir errores a la salida estándar

        // Ejecutar el comando
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
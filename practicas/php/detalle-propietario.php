<?php
$dbHost = "srv805.hstgr.io"; // Host proporcionado por Hostinger
$dbUser = "u666383048_clinica"; // Usuario de la base de datos
$dbPass = "9~o0jY:Xw"; // Contraseña del usuario
$dbName = "u666383048_clinica"; // Nombre de la base de datos
$dbPort = 3306; // Puerto de la base de datos (generalmente 3306 para MySQL)

// Establecer conexión con la base de datos
// Se incluye el puerto como un parámetro adicional en mysqli
$conn = new mysqli($dbHost, $dbUser, $dbPass, $dbName, $dbPort);
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$mascota_id = isset($_GET['mascota_id']) ? intval($_GET['mascota_id']) : 0;

/*
** Sección 1: Visualizar Información de Clientes y Mascotas
*/
$sql = "SELECT * FROM clientes WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();  // Almacenamos los datos del cliente
} else {
    die("No se encontró el propietario.");
}

$sql_mascotas = "SELECT * FROM mascotas WHERE propietario_id = ?";
$stmt_mascotas = $conn->prepare($sql_mascotas);
$stmt_mascotas->bind_param("i", $id);
$stmt_mascotas->execute();
$result_mascotas = $stmt_mascotas->get_result();

$mascota_detalles = null;
if ($mascota_id > 0) {
    $sql_mascota = "SELECT * FROM mascotas WHERE id = ?";
    $stmt_mascota = $conn->prepare($sql_mascota);
    $stmt_mascota->bind_param("i", $mascota_id);
    $stmt_mascota->execute();
    $result_mascota = $stmt_mascota->get_result();
    if ($result_mascota->num_rows > 0) {
        $mascota_detalles = $result_mascota->fetch_assoc();
    }
}

$sql_historial = "SELECT descripcion, fecha_visita FROM historial_visitas WHERE mascota_id = ? ORDER BY fecha_visita DESC";
$stmt_historial = $conn->prepare($sql_historial);
$stmt_historial->bind_param("i", $mascota_id);
$stmt_historial->execute();
$result_historial = $stmt_historial->get_result();

if ($result_historial->num_rows > 0) {
    $descripcion = $result_historial->fetch_assoc()['descripcion'];
} else {
    $descripcion = "Sin descripción registrada";  // Valor por defecto
}

/*
** Sección 2: Editar datos de Clientes y Mascotas
*/
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['editar_cliente'])) {
        // Recolectar valores del formulario
        $nuevo_nombre = $_POST['nombre'];
        $nueva_direccion = $_POST['direccion'];
        $nuevo_telefono = $_POST['telefono'];
        $nuevo_dni = $_POST['dni'];
        $nuevo_doctor = $_POST['doctor'];
        $nueva_fechaNacimiento = $_POST['fechaNacimiento'];
        $nueva_nacionalidad = $_POST['nacionalidad'];
        $nuevo_diagnostico = $_POST['diagnostico'];
        $nuevo_sexo = $_POST['sexo'];
        $nueva_especialidad = $_POST['especialidad'];
        $nueva_fechaSeguimientoInicio = $_POST['fechaSeguimientoInicio'];
        $nueva_descripcion = $_POST['descripcion'];

        // Actualizar en la base de datos
        $sql_editar = "UPDATE clientes SET
            nombre = ?,
            direccion = ?,
            telefono = ?,
            dni = ?,
            doctor = ?,
            fechaNacimiento = ?,
            nacionalidad = ?,
            diagnostico = ?,
            sexo = ?,
            especialidad = ?,
            fechaSeguimientoInicio = ?,
            descripcion = ?
            WHERE id = ?";

        $stmt_editar = $conn->prepare($sql_editar);
        $stmt_editar->bind_param("ssssssssssssi",
            $nuevo_nombre,
            $nueva_direccion,
            $nuevo_telefono,
            $nuevo_dni,
            $nuevo_doctor,
            $nueva_fechaNacimiento,
            $nueva_nacionalidad,
            $nuevo_diagnostico,
            $nuevo_sexo,
            $nueva_especialidad,
            $nueva_fechaSeguimientoInicio,
            $nueva_descripcion,
            $id
        );

        if ($stmt_editar->execute()) {
            $mensaje = "Datos del cliente actualizados correctamente.";
            // Refrescar datos en $row si es necesario
        } else {
            $mensaje = "Error al actualizar los datos del cliente: " . $stmt_editar->error;
        }
    }
}


    // Editar o insertar mascota con verificación de duplicados
    /*if (isset($_POST['editar_mascota'])) {
        $nuevo_nombre = $_POST['nombre'];
        $nueva_especie = $_POST['especie'];
        $nueva_raza = $_POST['raza'];
        $nuevo_color = $_POST['color'];
        $nuevo_sexo = $_POST['sexo'];
        $nueva_fecha_nacimiento = $_POST['fechaNacimiento'];

        // Verificar si ya existe una mascota con el mismo nombre y especie para este propietario
        $sql_verificar_mascota = "SELECT * FROM mascotas WHERE nombre = ? AND especie = ? AND propietario_id = ?";
        $stmt_verificar = $conn->prepare($sql_verificar_mascota);
        $stmt_verificar->bind_param("ssi", $nuevo_nombre, $nueva_especie, $id);
        $stmt_verificar->execute();
        $result_verificar = $stmt_verificar->get_result();

        if ($result_verificar->num_rows > 0) {
            // Si ya existe, actualizamos los datos de la mascota
            $mascota_detalles = $result_verificar->fetch_assoc();
            $mascota_id = $mascota_detalles['id']; // Obtener el ID de la mascota existente
            $mensaje_mascota = "La mascota ya existe, se actualizarán sus datos.";

            // Actualizar los datos de la mascota existente
            $sql_editar_mascota = "UPDATE mascotas SET nombre = ?, especie = ?, raza = ?, color = ?, sexo = ?, fechaNacimiento = ? WHERE id = ?";
            $stmt_editar_mascota = $conn->prepare($sql_editar_mascota);
            $stmt_editar_mascota->bind_param("ssssssi", $nuevo_nombre, $nueva_especie, $nueva_raza, $nuevo_color, $nuevo_sexo, $nueva_fecha_nacimiento, $mascota_id);

            if ($stmt_editar_mascota->execute()) {
                $mensaje_mascota = "Datos de la mascota actualizados correctamente.";
                $mascota_detalles['nombre'] = $nuevo_nombre;
                $mascota_detalles['especie'] = $nueva_especie;
                $mascota_detalles['raza'] = $nueva_raza;
                $mascota_detalles['color'] = $nuevo_color;
                $mascota_detalles['sexo'] = $nuevo_sexo;
                $mascota_detalles['fechaNacimiento'] = $nueva_fecha_nacimiento;
            } else {
                $mensaje_mascota = "Error al actualizar los datos de la mascota.";
            }
        } else {
            // Si no existe, insertamos la nueva mascota
            $sql_insertar_mascota = "INSERT INTO mascotas (nombre, especie, raza, color, sexo, fechaNacimiento, propietario_id) VALUES (?, ?, ?, ?, ?, ?, ?)";
            $stmt_insertar_mascota = $conn->prepare($sql_insertar_mascota);
            $stmt_insertar_mascota->bind_param("ssssssi", $nuevo_nombre, $nueva_especie, $nueva_raza, $nuevo_color, $nuevo_sexo, $nueva_fecha_nacimiento, $id);

            if ($stmt_insertar_mascota->execute()) {
                $mascota_id = $stmt_insertar_mascota->insert_id;  // Obtener el ID de la nueva mascota insertada
                $mensaje_mascota = "Mascota registrada correctamente.";
            } else {
                $mensaje_mascota = "Error al registrar la mascota.";
            }

    }*/


/*
** Sección 3: Registrar y Consultar Historial de Visitas de las Mascotas
*/
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['agregar_visita'])) {
        $descripcion_visita = $_POST['descripcion_visita'];
        $fecha_visita = $_POST['fecha_visita'];

        // Insertar historial de visita
        $sql_visita = "INSERT INTO historial_visitas (mascota_id, descripcion, fecha_visita) VALUES (?, ?, ?)";
        $stmt_visita = $conn->prepare($sql_visita);
        $stmt_visita->bind_param("iss", $mascota_id, $descripcion_visita, $fecha_visita);

        if ($stmt_visita->execute()) {
            $mensaje_visita = "Visita registrada correctamente.";
        } else {
            $mensaje_visita = "Error al registrar la visita.";
        }
    }
}



/*identificacionde id de la mascota */ 
/*
// Obtener el ID de la mascota de la URL
$mascota_id = isset($_GET['mascota_id']) ? intval($_GET['mascota_id']) : 0;

if ($mascota_id > 0) {
    // Consultar la información de la mascota y del cliente asociado
    $query = "SELECT m.*, c.id AS cliente_id, c.nombre AS cliente_nombre 
              FROM mascotas m
              JOIN clientes c ON m.cliente_id = c.id
              WHERE m.id = $mascota_id";
    $result = $conn->query($query);

    if ($result->num_rows > 0) {
        $data = $result->fetch_assoc();
        $cliente_id = $data['cliente_id'];
        $cliente_nombre = $data['cliente_nombre'];
        $mascota_nombre = $data['nombre'];
    } else {
        die("No se encontró información para la mascota seleccionada.");
    }
} else {
    die("No se proporcionó un ID válido para la mascota.");
}
$conn->close();*/
?>




<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalles del Propietario</title>
    <link rel="stylesheet" href="../css/ver-detalle.css">
    <link rel="stylesheet" href="../css/historial.css">
    <link rel="stylesheet" href="../css/historial2.0.css">
    
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
</head>
<body>
<div class="contenedor">
    <!-- Tarjeta de Presentación -->
    <div id="tarjeta" class="tarjeta">
        <h2>Detalles del Propietario</h2>
       
      <?php if (isset($mensaje)) { echo "<p>$mensaje</p>"; } ?>

<p><strong>ID:</strong> <?php echo $row['id']; ?></p>
<p><strong>Nombre del paciente:</strong> <?php echo $row['nombre']; ?></p>
<p><strong>Dirección:</strong> <?php echo $row['direccion']; ?></p>
<p><strong>Teléfono:</strong> <?php echo $row['telefono']; ?></p>
<p><strong>DNI:</strong> <?php echo $row['dni']; ?></p>
<p><strong>Doctor:</strong> <?php echo $row['doctor']; ?></p>
<p><strong>Fecha de nacimiento:</strong> <?php echo $row['fechaNacimiento']; ?></p>
<p><strong>Nacionalidad:</strong> <?php echo $row['nacionalidad']; ?></p>
<p><strong>Diagnóstico:</strong> <?php echo $row['diagnostico']; ?></p>
<p><strong>Sexo:</strong> <?php echo $row['sexo']; ?></p>
<p><strong>Especialidad:</strong> <?php echo $row['especialidad']; ?></p>
<p><strong>Fecha de seguimiento:</strong> <?php echo $row['fechaSeguimientoInicio']; ?></p>
<p><strong>Descripción:</strong> <?php echo $row['descripcion']; ?></p>


        <!-- Detalles de la Mascota -->
     <!--   <?php if ($mascota_detalles): ?>
        <div id="detalles-mascota" class="detalles-mascota">
            <h3>Detalles del</h3>
            <p><strong>Nombre:</strong> <?php echo $mascota_detalles['nombre']; ?></p>
            <p><strong>Especie:</strong> <?php echo $mascota_detalles['especie']; ?></p>
            <p><strong>Raza:</strong> <?php echo $mascota_detalles['raza']; ?></p>
            <p><strong>Color:</strong> <?php echo $mascota_detalles['color']; ?></p>
            <p><strong>Sexo:</strong> <?php echo $mascota_detalles['sexo']; ?></p>
            <p><strong>Fecha de Nacimiento:</strong> <?php echo $mascota_detalles['fechaNacimiento']; ?></p>
           <p><strong> descripcion: <?php echo isset($descripcion) ? htmlspecialchars($descripcion) : 'historial no disponible.';?></strong></p>
        </div>
        <?php endif; ?>-->
        
        <!-- Botones de acción -->
        <div class="button-container">
            <button onclick="document.getElementById('form-editar').style.display = 'block';">Editar</button>
            <a href="descargar_pdf.php?id=<?php echo $row['id']; ?>">Descargar en PDF</a>
            <button onclick="descargarTarjeta()">Descargar imagen</button>
            <button class="open-modal-btn">Agregar Descripción</button>
        </div>
    </div>

    <div id="historial" class="historial">
    <h3>Historial de Visitas</h3>
    <ul id="lista-historial">
       <!-- <li>No hay historial disponible.</li>-->
        <p><strong>Descripción:</strong> <?php echo $row['descripcion']; ?></p>
    </ul>
</div>



<!-- Modal -->
<!-- Formulario Modal -->
<!-- Modal -->
<div class="modal-overlay" id="modalOverlay">
    <div class="modal">
        <button class="close-modal-btn" id="closeModalBtn">X</button>
        <h2>Agregar Descripción</h2>
        <form id="addDescriptionForm">
            <input type="hidden" id="cliente_id" name="cliente_id">
            <input type="hidden" id="mascota_id" name="mascota_id">

            <label for="fecha_visita">Fecha de la Visita:</label>
            <input type="date" id="fecha_visita" name="fecha_visita" required>

            <label for="descripcion">Descripción:</label>
            <textarea id="descripcion" name="descripcion" rows="4" required></textarea>

            <button type="submit">Guardar</button>
        </form>
    </div>
</div>


<script>
    // Elementos del DOM
    const openModalBtn = document.querySelector('.open-modal-btn');
    const modalOverlay = document.getElementById('modalOverlay');
    const closeModalBtn = document.getElementById('closeModalBtn');
    const form = document.getElementById('addForm');
    const listaHistorial = document.getElementById('lista-historial');

    // Abrir el modal
    openModalBtn.addEventListener('click', () => {
        modalOverlay.style.display = 'flex';
    });

    // Cerrar el modal
    closeModalBtn.addEventListener('click', () => {
        modalOverlay.style.display = 'none';
    });

    // Cerrar el modal al hacer clic fuera del formulario
    modalOverlay.addEventListener('click', (event) => {
        if (event.target === modalOverlay) {
            modalOverlay.style.display = 'none';
        }
    });

    // Manejar el formulario
    form.addEventListener('submit', async (event) => {
        event.preventDefault();

        // Capturar datos del formulario
        const clienteId = document.getElementById('cliente_id').value;
        const mascotaId = document.getElementById('mascota_id').value;
        const fechaVisita = document.getElementById('fecha_visita').value;
        const descripcion = document.getElementById('descripcion').value;

        // Enviar datos al servidor con fetch
        const formData = new FormData();
        formData.append('cliente_id', clienteId);
        formData.append('mascota_id', mascotaId);
        formData.append('fecha_visita', fechaVisita);
        formData.append('descripcion', descripcion);

        try {
            const response = await fetch('agregar-historial.php', {
                method: 'POST',
                body: formData,
            });

            const result = await response.text();

            if (response.ok) {
                alert('¡Información agregada con éxito!');
                modalOverlay.style.display = 'none';
                cargarHistorial(clienteId); // Actualizar historial dinámicamente
            } else {
                alert('Error al agregar la descripción: ' + result);
            }
        } catch (error) {
            console.error('Error en la solicitud:', error);
        }
    });

    // Función para cargar historial dinámicamente
    async function cargarHistorial(clienteId) {
        try {
            const response = await fetch(`agregar-historial.php?cliente_id=${clienteId}`);
            const historialHtml = await response.text();
            listaHistorial.innerHTML = historialHtml;
        } catch (error) {
            console.error('Error al cargar el historial:', error);
        }
    }
</script>



<script src="../jss"></script>
</div>
    
    <div id="form-editar" style="display:none;">
  <h3>Editar Cliente</h3>
<form method="POST">
    <label for="nombre">Nombre del paciente:</label>
    <input type="text" id="nombre" name="nombre" value="<?php echo isset($row['nombre']) ? $row['nombre'] : ''; ?>" required>
    <label for="direccion">Dirección:</label>
    <input type="text" id="direccion" name="direccion" value="<?php echo isset($row['direccion']) ? $row['direccion'] : ''; ?>" required>
    <label for="telefono">Teléfono:</label>
    <input type="text" id="telefono" name="telefono" value="<?php echo isset($row['telefono']) ? $row['telefono'] : ''; ?>" required>
    <label for="dni">DNI:</label>
    <input type="text" id="dni" name="dni" value="<?php echo isset($row['dni']) ? $row['dni'] : ''; ?>" required>
    <label for="doctor">Doctor:</label>
    <input type="text" id="doctor" name="doctor" value="<?php echo isset($row['doctor']) ? $row['doctor'] : ''; ?>" required>
    <label for="fechaNacimiento">Fecha de Nacimiento:</label>
    <input type="date" id="fechaNacimiento" name="fechaNacimiento" value="<?php echo isset($row['fechaNacimiento']) ? $row['fechaNacimiento'] : ''; ?>" required>
    <label for="nacionalidad">Nacionalidad:</label>
    <input type="text" id="nacionalidad" name="nacionalidad" value="<?php echo isset($row['nacionalidad']) ? $row['nacionalidad'] : ''; ?>" required>
    <label for="diagnostico">Diagnóstico:</label>
    <input type="text" id="diagnostico" name="diagnostico" value="<?php echo isset($row['diagnostico']) ? $row['diagnostico'] : ''; ?>" required>
    <label for="sexo">Sexo:</label>
    <select id="sexo" name="sexo" required>
        <option value="masculino" <?php echo (isset($row['sexo']) && $row['sexo'] == 'masculino') ? 'selected' : ''; ?>>Masculino</option>
        <option value="femenino" <?php echo (isset($row['sexo']) && $row['sexo'] == 'femenino') ? 'selected' : ''; ?>>Femenino</option>
    </select>
    <label for="especialidad">Especialidad:</label>
    <input type="text" id="especialidad" name="especialidad" value="<?php echo isset($row['especialidad']) ? $row['especialidad'] : ''; ?>" required>
    <label for="fechaSeguimientoInicio">Fecha de Registro / Seguimiento:</label>
    <input type="date" id="fechaSeguimientoInicio" name="fechaSeguimientoInicio" value="<?php echo isset($row['fechaSeguimientoInicio']) ? $row['fechaSeguimientoInicio'] : ''; ?>" required>
    <label for="descripcion">Descripción:</label>
    <textarea id="descripcion" name="descripcion" required><?php echo isset($row['descripcion']) ? $row['descripcion'] : ''; ?></textarea>
    <button type="submit" name="editar_cliente">Guardar Cambios</button>
</form>

<!--<h3>Editar Mascota</h3>
<form method="POST">
    <label for="nombre">Nombre:</label>
    <input type="text" id="nombre" name="nombre" value="<?php echo isset($mascota_detalles['nombre']) ? $mascota_detalles['nombre'] : ''; ?>" required>
    
    <label for="especie">Especie:</label>
    <select id="especie" name="especie" required>
        <option value="Canino" <?php echo (isset($mascota_detalles['especie']) && $mascota_detalles['especie'] == 'Canino') ? 'selected' : ''; ?>>Canino</option>
        <option value="Felino" <?php echo (isset($mascota_detalles['especie']) && $mascota_detalles['especie'] == 'Felino') ? 'selected' : ''; ?>>Felino</option>
        <option value="Aves" <?php echo (isset($mascota_detalles['especie']) && $mascota_detalles['especie'] == 'Aves') ? 'selected' : ''; ?>>Aves</option>
        <option value="Lagomorfos" <?php echo (isset($mascota_detalles['especie']) && $mascota_detalles['especie'] == 'Lagomorfos') ? 'selected' : ''; ?>>Lagomorfos</option>
        <option value="Otros" <?php echo (isset($mascota_detalles['especie']) && $mascota_detalles['especie'] == 'Otros') ? 'selected' : ''; ?>>Otros</option>
    </select><br>

    <label for="raza">Raza:</label>
    <input type="text" id="raza" name="raza" value="<?php echo isset($mascota_detalles['raza']) ? $mascota_detalles['raza'] : ''; ?>" required>
    
    <label for="color">Color:</label>
    <input type="text" id="color" name="color" value="<?php echo isset($mascota_detalles['color']) ? $mascota_detalles['color'] : ''; ?>" required>

    <label for="sexo">Sexo:</label>
    <select id="sexo" name="sexo" required>
        <option value="macho" <?php echo (isset($mascota_detalles['sexo']) && $mascota_detalles['sexo'] == 'macho') ? 'selected' : ''; ?>>Macho</option>
        <option value="hermbra" <?php echo (isset($mascota_detalles['sexo']) && $mascota_detalles['sexo'] == 'hembra') ? 'selected' : ''; ?>>Hembra</option>
    </select><br>
    
    <label for="fechaNacimiento">Fecha de Nacimiento:</label>
    <input type="date" id="fechaNacimiento" name="fechaNacimiento" value="<?php echo isset($mascota_detalles['fechaNacimiento']) ? $mascota_detalles['fechaNacimiento'] : ''; ?>" required>
    
    <button type="submit" name="editar_mascota">Guardar Cambios</button>
</form>-->


</div>
<script>
        function descargarTarjeta() {
            const tarjeta = document.getElementById('tarjeta');
            
            html2canvas(tarjeta).then((canvas) => {
                const imagen = canvas.toDataURL("image/png");
                const enlace = document.createElement("a");
                enlace.href = imagen;
                enlace.download = "TARJETA DEL PACIENTE.png";
                enlace.click();
            });
        }
    </script>

</div>

<div class="cuadro-mascotas">
    <h3>OTRAS CONSULTAS DEL PACIENTE</h3>
    <?php if ($result_mascotas->num_rows > 0): ?>
        <ul>
            <?php while ($mascota = $result_mascotas->fetch_assoc()): ?>
                <li>
                    <strong>Nombre:</strong> <?php echo $mascota['nombre']; ?>, 
                 <!--   <strong>Especie:</strong> <?php echo $mascota['especie']; ?>, -->
                    <a href="detalle-propietario.php?id=<?php echo $id; ?>&mascota_id=<?php echo $mascota['id']; ?>"><button>Más información</button></a>
                </li>
            <?php endwhile; ?>
        </ul>
    <?php else: ?>
        <p>No hay mascotas registradas para este propietario.</p>
    <?php endif; ?>
</div>



<!--<button onclick="window.history.back();" class="btn-volver">Regresar</button>-->
<div class="volver">
    <a href="ventanas.php" class="btn-volver">volver a gestion de pacientes</a>
</div>



</body>
</html>
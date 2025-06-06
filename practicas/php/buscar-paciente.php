<?php
$host = 'localhost';
$user = 'root';
$password = '';
$database = 'veterinaria';

$conn = new mysqli($host, $user, $password, $database);
if ($conn->connect_error) {
    die('Error de conexión: ' . $conn->connect_error);
}

$conn->set_charset("utf8mb4");

// Obtener los parámetros enviados por POST
$cliente_id = isset($_POST['cliente_id']) ? (int)$_POST['cliente_id'] : 0;
$nombre = isset($_POST['nombre']) ? trim($_POST['nombre']) : ''; // Sin cambios de mayúsculas/minúsculas por ahora
$pagina = isset($_POST['pagina']) ? (int)$_POST['pagina'] : 1;
$resultadosPorPagina = 1; // Una mascota por página
$inicio = ($pagina - 1) * $resultadosPorPagina;

// Construir la consulta dinámica
$where = [];
$parametros = [];
$tipos = "";

// Filtro por ID del cliente
if ($cliente_id > 0) {
    $where[] = "c.id = ?";
    $parametros[] = $cliente_id;
    $tipos .= "i";
}

// Filtro por nombre del propietario
if (!empty($nombre)) {
    $where[] = "LOWER(c.propietario) LIKE ?";
    $parametros[] = "%" . strtolower($nombre) . "%"; // Convertir a minúsculas para comparación
    $tipos .= "s";
}

// Si no hay filtros, mostrar mensaje de error
if (count($where) === 0) {
    die("<p>Por favor ingresa un ID o un nombre para buscar.</p>");
}

// Construir la cláusula WHERE final
$where_clause = "WHERE " . implode(" AND ", $where);

// Consulta principal con paginación
$sql = "
    SELECT 
        c.id AS cliente_id,
        c.propietario,
        c.direccion,
        c.telefono,
        c.dni,
        hv.descripcion AS historial_descripcion,
        m.nombre AS mascota_nombre,
        m.fechaNacimiento AS mascota_fechaNacimiento,
        m.especie AS mascota_especie,
        m.raza AS mascota_raza,
        m.sexo AS mascota_sexo,
        m.color AS mascota_color
    FROM
        clientes c
    JOIN
        mascotas m
    ON
        c.id = m.propietario_id
    LEFT JOIN
        historial_visitas hv
    ON
        c.id = hv.cliente_id AND m.id = hv.mascota_id
    $where_clause
    LIMIT ?, ?";

// Preparar la consulta
$stmt = $conn->prepare($sql);

if (!$stmt) {
    die("Error en la consulta: " . $conn->error);
}

// Agregar parámetros de paginación
$parametros[] = $inicio;
$parametros[] = $resultadosPorPagina;
$tipos .= "ii";

// Vincular los parámetros a la consulta
$stmt->bind_param($tipos, ...$parametros);
$stmt->execute();
$result = $stmt->get_result();

// Mostrar resultados
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo "<h2>Detalles del Cliente</h2>";
        echo "<p><strong>ID:</strong> {$row['cliente_id']}</p>";
        echo "<p><strong>Propietario:</strong> {$row['propietario']}</p>";
        echo "<p><strong>Dirección:</strong> {$row['direccion']}</p>";
        echo "<p><strong>Teléfono:</strong> {$row['telefono']}</p>";
        echo "<p><strong>DNI:</strong> {$row['dni']}</p>";

        echo "<h3>Detalles de la Mascota</h3>";
        echo "<p><strong>Nombre:</strong> {$row['mascota_nombre']}</p>";
        echo "<p><strong>Fecha de Nacimiento:</strong> {$row['mascota_fechaNacimiento']}</p>";
        echo "<p><strong>Especie:</strong> {$row['mascota_especie']}</p>";
        echo "<p><strong>Raza:</strong> {$row['mascota_raza']}</p>";
        echo "<p><strong>Sexo:</strong> {$row['mascota_sexo']}</p>";
        echo "<p><strong>Color:</strong> {$row['mascota_color']}</p>";
        echo "<p><strong>Descripción del historial:</strong> " . ($row['historial_descripcion'] ?: 'Sin descripción') . "</p>";
    }
} else {
    echo "<p>No se encontraron resultados para los criterios de búsqueda.</p>";
}

// Obtener el total de registros para la paginación
$sqlTotal = "SELECT COUNT(*) AS total FROM clientes c JOIN mascotas m ON c.id = m.propietario_id $where_clause";
$stmtTotal = $conn->prepare($sqlTotal);

if (!$stmtTotal) {
    die("Error en la consulta de total: " . $conn->error);
}

// Vincular parámetros al cálculo de total (excluyendo los límites de paginación)
if (!empty($parametros)) {
    $stmtTotal->bind_param(substr($tipos, 0, -2), ...array_slice($parametros, 0, -2));
}
$stmtTotal->execute();
$resultTotal = $stmtTotal->get_result();
$totalRegistros = $resultTotal->fetch_assoc()['total'];
$totalPaginas = ceil($totalRegistros / $resultadosPorPagina);

// Mostrar la paginación
echo "<div class='paginacion'>";
for ($i = 1; $i <= $totalPaginas; $i++) {
    $clase = ($i == $pagina) ? 'activo' : '';
    echo "<a href='javascript:void(0)' class='pagina-btn $clase' onclick='cargarPagina($i)'>$i</a>";
}
echo "</div>";

// Cerrar la conexión
$stmt->close();
$conn->close();
?>

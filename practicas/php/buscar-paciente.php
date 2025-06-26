<?php
$dbHost = "srv805.hstgr.io";
$dbUser = "u666383048_clinica";
$dbPass = "9~o0jY:Xw";
$dbName = "u666383048_clinica";
$dbPort = 3306;

$conn = new mysqli($dbHost, $dbUser, $dbPass, $dbName, $dbPort);
if ($conn->connect_error) {
    die('Error de conexión: ' . $conn->connect_error);
}

$conn->set_charset("utf8mb4");

$cliente_id = isset($_POST['cliente_id']) ? (int)$_POST['cliente_id'] : 0;
$nombre = isset($_POST['nombre']) ? trim($_POST['nombre']) : '';
$pagina = isset($_POST['pagina']) ? (int)$_POST['pagina'] : 1;
$resultadosPorPagina = 1; // Una consulta por página, dentro de un paciente
$inicio = ($pagina - 1) * $resultadosPorPagina;

$where = [];
$parametros = [];
$tipos = "";

// Filtro por ID del paciente
if ($cliente_id > 0) {
    $where[] = "p.id = ?";
    $parametros[] = $cliente_id;
    $tipos .= "i";
}

// Filtro por nombre del paciente
if (!empty($nombre)) {
    $where[] = "LOWER(p.nombre) LIKE ?";
    $parametros[] = "%" . strtolower($nombre) . "%";
    $tipos .= "s";
}

if (count($where) === 0) {
    die("<p>Por favor ingresa un ID o un nombre para buscar.</p>");
}

$where_clause = "WHERE " . implode(" AND ", $where);

// Consulta principal para obtener detalles del paciente y sus consultas.
// Nota: Aquí se seleccionarán los datos del paciente (p) y de una consulta (cm).
// La paginación se aplicará sobre las consultas de un paciente específico.
$sql = "
    SELECT 
        p.id AS paciente_id,
        p.nombre AS paciente_nombre,
        p.direccion,
        p.telefono,
        p.dni,
        p.fechaNacimiento AS paciente_fechaNacimiento,
        p.nacionalidad AS paciente_nacionalidad,
        -- Datos de la consulta médica (antes mascotas)
        cm.id AS consulta_id,
        cm.nombre_consulta,
        cm.diagnostico_breve,
        cm.sexo,
        cm.especialidad,
        cm.fecha_consulta,
        cm.diagnostico_detallado,
        -- Datos de la última visita de detalle (antes historial_visitas)
        hv.descripcion AS visitas_detalle_descripcion
    FROM
        pacientes p
    JOIN
        consultas_medicas cm ON p.id = cm.paciente_id
    LEFT JOIN (
        SELECT 
            consulta_id, 
            descripcion,
            ROW_NUMBER() OVER(PARTITION BY consulta_id ORDER BY fecha_visita DESC, id DESC) as rn
        FROM 
            visitas_detalle
    ) hv ON cm.id = hv.consulta_id AND hv.rn = 1 -- Obtiene solo la última descripción de visita por consulta
    $where_clause
    ORDER BY
        p.id, cm.fecha_consulta DESC, cm.id DESC -- Ordenar para consistencia en la paginación de consultas
    LIMIT ?, ?";

$stmt = $conn->prepare($sql);

if (!$stmt) {
    die("Error en la consulta: " . $conn->error . " SQL: " . $sql); // Añadido SQL para depuración
}

$parametros[] = $inicio;
$parametros[] = $resultadosPorPagina;
$tipos .= "ii"; // Dos 'i' para LIMIT y OFFSET

$stmt->bind_param($tipos, ...$parametros);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo "<h2>Detalles del Paciente</h2>";
        echo "<p><strong>ID del Paciente:</strong> {$row['paciente_id']}</p>";
        echo "<p><strong>Nombre del Paciente:</strong> {$row['paciente_nombre']}</p>";
        echo "<p><strong>Dirección:</strong> {$row['direccion']}</p>";
        echo "<p><strong>Teléfono:</strong> {$row['telefono']}</p>";
        echo "<p><strong>DNI:</strong> {$row['dni']}</p>";
        echo "<p><strong>Fecha de Nacimiento del Paciente:</strong> {$row['paciente_fechaNacimiento']}</p>";
        echo "<p><strong>Nacionalidad del Paciente:</strong> {$row['paciente_nacionalidad']}</p>";
        
        echo "<h3>Detalles de la Consulta</h3>";
        echo "<p><strong>ID de Consulta:</strong> {$row['consulta_id']}</p>";
        echo "<p><strong>Nombre de Consulta:</strong> {$row['nombre_consulta']}</p>";
        echo "<p><strong>Diagnóstico Breve:</strong> {$row['diagnostico_breve']}</p>";
        echo "<p><strong>Sexo de la Consulta:</strong> {$row['sexo']}</p>";
        echo "<p><strong>Especialidad de la Consulta:</strong> {$row['especialidad']}</p>";
        echo "<p><strong>Fecha de Consulta:</strong> {$row['fecha_consulta']}</p>";
        echo "<p><strong>Diagnóstico Detallado:</strong> {$row['diagnostico_detallado']}</p>";
        echo "<p><strong>Última Descripción de Visita:</strong> " . ($row['visitas_detalle_descripcion'] ?: 'Sin descripción de visita') . "</p>";
    }
} else {
    echo "<p>No se encontraron resultados para el búsqueda o consulta.</p>";
}

// Obtener el total de consultas para la paginación, basado en el filtro de paciente
$sqlTotal = "SELECT COUNT(*) AS total FROM pacientes p JOIN consultas_medicas cm ON p.id = cm.paciente_id $where_clause";
$stmtTotal = $conn->prepare($sqlTotal);

if (!$stmtTotal) {
    die("Error en la consulta de total: " . $conn->error . " SQL: " . $sqlTotal);
}

// Vincular parámetros al cálculo de total (los mismos filtros que la consulta principal)
// Excluimos los últimos dos parámetros ('limit' y 'offset') que eran solo para la consulta principal.
$parametros_total = array_slice($parametros, 0, -2);
$tipos_total = substr($tipos, 0, -2);

if (!empty($parametros_total)) {
    $stmtTotal->bind_param($tipos_total, ...$parametros_total);
}

$stmtTotal->execute();
$resultTotal = $stmtTotal->get_result();
$totalRegistros = $resultTotal->fetch_assoc()['total'];
$totalPaginas = ceil($totalRegistros / $resultadosPorPagina);

echo "<div class='paginacion'>";
for ($i = 1; $i <= $totalPaginas; $i++) {
    $clase = ($i == $pagina) ? 'activo' : '';
    echo "<a href='javascript:void(0)' class='pagina-btn $clase' onclick='cargarPagina($i)'>$i</a>";
}
echo "</div>";

if ($stmt) {
    $stmt->close();
}
if ($stmtTotal) {
    $stmtTotal->close();
}
$conn->close();
?>
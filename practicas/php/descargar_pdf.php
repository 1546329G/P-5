<?php
// C:\xampp\htdocs\P-5\practicas\php\detalle-propietario.php

// Descargar PDF de los detalles del propietario
require('../libs/fpdf/fpdf.php'); // Asegúrate de incluir la librería FPDF correctamente

// --- Configuración de la conexión a la base de datos ---
$dbHost = "srv805.hstgr.io"; // Host proporcionado por Hostinger
$dbUser = "u666383048_clinica"; // Usuario de la base de datos
$dbPass = "9~o0jY:Xw"; // Contraseña del usuario
$dbName = "u666383048_clinica"; // Nombre de la base de datos
$dbPort = 3306; // Puerto de la base de datos (generalmente 3306 para MySQL)

// Establecer conexión con la base de datos
$conn = new mysqli($dbHost, $dbUser, $dbPass, $dbName, $dbPort);
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}
$conn->set_charset("utf8mb4");

// Obtener el ID del cliente de la URL
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($id <= 0) {
    die("ID de propietario no válido.");
}

// --- Consulta del propietario (clientes) ---
// Seleccionamos todas las columnas según tu tabla 'clientes'
$sql_cliente = "SELECT id, direccion, telefono, dni, doctor, nombre, fechaNacimiento, nacionalidad, diagnostico, sexo, especialidad, fechaSeguimientoInicio, descripcion FROM clientes WHERE id = ?";
$stmt_cliente = $conn->prepare($sql_cliente);

// Verificar si la preparación de la consulta de cliente falló
if ($stmt_cliente === false) {
    die("Error al preparar la consulta del cliente: " . $conn->error);
}

$bind_cliente_success = $stmt_cliente->bind_param("i", $id);
if ($bind_cliente_success === false) {
    die("Error en bind_param para cliente: " . $stmt_cliente->error);
}

$stmt_cliente->execute();
$result_cliente = $stmt_cliente->get_result();

if ($result_cliente->num_rows > 0) {
    $cliente = $result_cliente->fetch_assoc();
} else {
    die("Propietario no encontrado.");
}
$stmt_cliente->close(); // Cerrar la sentencia del cliente

// --- Crear PDF ---
$pdf = new FPDF();
$pdf->AddPage();
$pdf->SetFont('Arial', 'B', 16);

// Encabezado
$pdf->SetTextColor(0, 51, 102); // Color azul oscuro
$pdf->Cell(0, 10, 'Detalles del PACIENTE', 0, 1, 'C'); // Título más general
$pdf->Ln(10);

// Separador decorativo
$pdf->SetDrawColor(0, 51, 102);
$pdf->SetLineWidth(0.5);
$pdf->Line(10, 30, 200, 30); // Línea horizontal
$pdf->Ln(5);

// --- Sección de Detalles del Cliente ---
$pdf->SetFont('Arial', 'B', 14);
$pdf->SetTextColor(0, 51, 102);
$pdf->Cell(0, 10, 'Informacion del Cliente', 0, 1, 'L');
$pdf->Ln(5);

$pdf->SetFont('Arial', 'B', 12);
$pdf->SetFillColor(200, 220, 255); // Color de fondo para encabezado de tabla
$pdf->SetTextColor(0, 51, 102); // Color del texto

// Fila 1: ID y DNI del Cliente
$pdf->Cell(50, 10, 'ID del Cliente:', 1, 0, 'L', true);
$pdf->Cell(45, 10, $cliente['id'], 1, 0, 'L');
$pdf->Cell(50, 10, 'DNI del Cliente:', 1, 0, 'L', true);
$pdf->Cell(45, 10, $cliente['dni'], 1, 1, 'L');

// Fila 2: Nombre del Cliente
$pdf->Cell(50, 10, 'Nombre del Cliente:', 1, 0, 'L', true);
$pdf->Cell(0, 10, $cliente['nombre'], 1, 1, 'L');

// Fila 3: Doctor Asignado
$pdf->Cell(50, 10, 'Doctor Asignado:', 1, 0, 'L', true);
$pdf->Cell(0, 10, $cliente['doctor'], 1, 1, 'L');

// Fila 4: Dirección
$pdf->Cell(50, 10, 'Direccion:', 1, 0, 'L', true);
$pdf->Cell(0, 10, $cliente['direccion'], 1, 1, 'L');

// Fila 5: Teléfono
$pdf->Cell(50, 10, 'Telefono:', 1, 0, 'L', true);
$pdf->Cell(0, 10, $cliente['telefono'], 1, 1, 'L');

// Fila 6: Fecha de Nacimiento del Cliente (si aplica y la estás usando así)
$pdf->Cell(50, 10, 'Fecha Nac. Cliente:', 1, 0, 'L', true);
$pdf->Cell(0, 10, date("d/m/Y", strtotime($cliente['fechaNacimiento'])), 1, 1, 'L'); // Mapeado a fechaNacimiento del cliente

// Fila 7: Nacionalidad del Cliente (si aplica y la estás usando así)
$pdf->Cell(50, 10, 'Nacionalidad Cliente:', 1, 0, 'L', true);
$pdf->Cell(0, 10, $cliente['nacionalidad'], 1, 1, 'L'); // Mapeado a nacionalidad del cliente

// Fila 8: Diagnostico del Cliente (si aplica y la estás usando así)
$pdf->Cell(50, 10, 'Diagnostico Cliente:', 1, 0, 'L', true);
$pdf->Cell(0, 10, $cliente['diagnostico'], 1, 1, 'L'); // Mapeado a diagnostico del cliente

// Fila 9: Sexo del Cliente (si aplica y la estás usando así)
$pdf->Cell(50, 10, 'Sexo Cliente:', 1, 0, 'L', true);
$pdf->Cell(0, 10, $cliente['sexo'], 1, 1, 'L'); // Mapeado a sexo del cliente

// Fila 10: Especialidad del Cliente (si aplica y la estás usando así)
$pdf->Cell(50, 10, 'Especialidad Cliente:', 1, 0, 'L', true);
$pdf->Cell(0, 10, $cliente['especialidad'], 1, 1, 'L'); // Mapeado a especialidad del cliente

// Fila 11: Fecha de Seguimiento del Cliente
$pdf->Cell(50, 10, 'Fecha Seguimiento:', 1, 0, 'L', true);
$pdf->Cell(0, 10, date("d/m/Y", strtotime($cliente['fechaSeguimientoInicio'])), 1, 1, 'L');

// Fila 12: Descripción del Cliente
$pdf->Cell(50, 10, 'Descripcion Cliente:', 1, 0, 'L', true);
$pdf->MultiCell(0, 10, $cliente['descripcion'], 1, 'L'); // Usa MultiCell para descripciones largas

$pdf->Ln(10); // Espaciado




//=============================================
//=============================================
// --- Sección de OTRAS CONSULTAS del Cliente ---
//=============================================
//=============================================


// --- Sección de OTRAS CONSULTAS del Cliente ---
$pdf->SetFont('Arial', 'B', 14);
$pdf->SetTextColor(0, 51, 102);
$pdf->Cell(0, 10, 'OTRAS CONMSULTAS:', 0, 1, 'L');
$pdf->Ln(5);

// Consultar todas las mascotas del propietario (no solo LIMIT 1)
$sql_mascotas = "SELECT id, nombre, nacionalidad, diagnostico, sexo, especialidad, fechaNacimiento FROM mascotas WHERE propietario_id = ?";
$stmt_mascotas = $conn->prepare($sql_mascotas);

// Verificar si la preparación de la consulta de mascotas falló
if ($stmt_mascotas === false) {
    die("Error al preparar la consulta de mascotas: " . $conn->error);
}

$bind_mascotas_success = $stmt_mascotas->bind_param("i", $id);
if ($bind_mascotas_success === false) {
    die("Error en bind_param para mascotas: " . $stmt_mascotas->error);
}

$stmt_mascotas->execute();
$result_mascotas = $stmt_mascotas->get_result();

// Tabla de mascota(s)
$pdf->SetFont('Arial', 'B', 10); // Fuente más pequeña para encabezados de tabla de mascotas
$pdf->SetFillColor(200, 200, 200); // Fondo gris claro para encabezados
$pdf->SetTextColor(0, 0, 0); // Texto negro
$pdf->SetDrawColor(200, 200, 200); // Bordes gris claro

// Nombres de columnas según tu tabla 'mascotas' y la información relevante
$pdf->Cell(30, 10, 'Nombre', 1, 0, 'C', true);
$pdf->Cell(30, 10, 'Nacionalidad', 1, 0, 'C', true); // Corresponde a 'especie'
$pdf->Cell(30, 10, 'Diagnostico', 1, 0, 'C', true); // Corresponde a 'raza'
$pdf->Cell(20, 10, 'Sexo', 1, 0, 'C', true);
$pdf->Cell(40, 10, 'Especialidad', 1, 0, 'C', true); // Corresponde a 'color'
$pdf->Cell(40, 10, 'Fecha Nac.', 1, 1, 'C', true);

// Datos de la mascota(s)
$pdf->SetFont('Arial', '', 10); // Fuente más pequeña para datos de mascotas
$pdf->SetFillColor(240, 240, 240); // Fondo gris claro para filas
$pdf->SetTextColor(0, 0, 0); // Texto negro

if ($result_mascotas->num_rows > 0) {
    while ($mascota = $result_mascotas->fetch_assoc()) {
        $pdf->Cell(30, 10, $mascota['nombre'], 1, 0, 'L', true);
        $pdf->Cell(30, 10, $mascota['nacionalidad'], 1, 0, 'L', true); // Mapeo a 'especie'
        $pdf->Cell(30, 10, $mascota['diagnostico'], 1, 0, 'L', true); // Mapeo a 'raza'
        $pdf->Cell(20, 10, $mascota['sexo'], 1, 0, 'L', true);
        $pdf->Cell(40, 10, $mascota['especialidad'], 1, 0, 'L', true); // Mapeo a 'color'
        $pdf->Cell(40, 10, date("d/m/Y", strtotime($mascota['fechaNacimiento'])), 1, 1, 'L', true);
    }
} else {
    $pdf->Cell(0, 10, 'No hay mascotas registradas para este cliente.', 1, 1, 'L', true);
}
$stmt_mascotas->close(); // Cerrar la sentencia de mascotas

$pdf->Ln(10); // Espaciado

//============================================
//=============================================
// --- Notas o Historial de Visitas del Cliente ---
//=============================================
//=============================================



// --- Notas o Historial de Visitas (sin columna 'descripcion' en historial_visitas) ---
$pdf->SetFont('Arial', 'B', 12);
$pdf->SetTextColor(0, 51, 102);
$pdf->Cell(0, 10, 'Historial de Visitas (Solo fechas):', 0, 1, 'L');
$pdf->Ln(5);

$pdf->SetFont('Arial', '', 12);
$pdf->SetFillColor(240, 240, 240); // Fondo gris claro
$pdf->SetTextColor(0, 0, 0); // Texto negro

// Consulta solo las fechas de visita (ya que 'descripcion' no está en historial_visitas)
// Si quieres una descripción, debe venir de 'clientes' o debes modificar 'historial_visitas'
$sql_historial_fechas = "SELECT fecha_visita FROM historial_visitas WHERE cliente_id = ? ORDER BY fecha_visita DESC";
$stmt_historial_fechas = $conn->prepare($sql_historial_fechas);

// Verificar si la preparación de la consulta de historial falló
if ($stmt_historial_fechas === false) {
    die("Error al preparar la consulta de historial de visitas: " . $conn->error);
}

$bind_historial_success = $stmt_historial_fechas->bind_param("i", $id);
if ($bind_historial_success === false) {
    die("Error en bind_param para historial de visitas: " . $stmt_historial_fechas->error);
}

$stmt_historial_fechas->execute();
$result_historial_fechas = $stmt_historial_fechas->get_result();

if ($result_historial_fechas->num_rows > 0) {
    $fechas_visitas = [];
    while ($row = $result_historial_fechas->fetch_assoc()) {
        $fechas_visitas[] = date("d/m/Y", strtotime($row['fecha_visita']));
    }
    $pdf->MultiCell(0, 10, "Fechas de Visitas: " . implode(", ", $fechas_visitas), 1, 'L', true);
} else {
    $pdf->Cell(0, 10, 'No hay visitas registradas en el historial para este cliente.', 1, 1, 'L', true);
}
$stmt_historial_fechas->close(); // Cerrar la sentencia del historial

$pdf->Ln(10); // Espaciado

// Salida del PDF
$pdf->Output('D', 'detalles_cliente_mascota_' . $id . '.pdf'); // Descargar el PDF
$conn->close();
?>
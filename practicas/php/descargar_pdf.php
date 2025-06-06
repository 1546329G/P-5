<?php
require('../libs/fpdf/fpdf.php'); // Asegúrate de incluir la librería FPDF

$conn = new mysqli("localhost", "root", "", "veterinaria");

if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Consulta del propietario (clientes)
$sql_cliente = "SELECT * FROM clientes WHERE id = ?";
$stmt_cliente = $conn->prepare($sql_cliente);
$stmt_cliente->bind_param("i", $id);
$stmt_cliente->execute();
$result_cliente = $stmt_cliente->get_result();

if ($result_cliente->num_rows > 0) {
    $cliente = $result_cliente->fetch_assoc();
} else {
    die("Propietario no encontrado.");
}

// Crear PDF
$pdf = new FPDF();
$pdf->AddPage();
$pdf->SetFont('Arial', 'B', 16);

// Encabezado
$pdf->SetTextColor(0, 51, 102); // Color azul oscuro
$pdf->Cell(0, 10, 'Detalles del Propietario', 0, 1, 'C');
$pdf->Ln(10);

// Separador decorativo
$pdf->SetDrawColor(0, 51, 102);
$pdf->SetLineWidth(0.5);
$pdf->Line(10, 30, 200, 30); // Línea horizontal
$pdf->Ln(5);

// Estilo de tabla
$pdf->SetFont('Arial', 'B', 12);
$pdf->SetFillColor(200, 220, 255); // Color de fondo para encabezado de tabla
$pdf->SetTextColor(0, 51, 102); // Color del texto

// Encabezado de la tabla
$pdf->Cell(50, 10, 'ID:', 1, 0, 'L', true);
$pdf->Cell(0, 10, $cliente['id'], 1, 1, 'L');

// Segunda fila
$pdf->Cell(50, 10, 'Propietario:', 1, 0, 'L', true);
$pdf->Cell(0, 10, $cliente['propietario'], 1, 1, 'L');

// Tercera fila
$pdf->Cell(50, 10, 'Direccion:', 1, 0, 'L', true);
$pdf->Cell(0, 10, $cliente['direccion'], 1, 1, 'L');

// Cuarta fila
$pdf->Cell(50, 10, 'Telefono:', 1, 0, 'L', true);
$pdf->Cell(0, 10, $cliente['telefono'], 1, 1, 'L');

// Quinta fila
$pdf->Cell(50, 10, 'DNI:', 1, 0, 'L', true);
$pdf->Cell(0, 10, $cliente['dni'], 1, 1, 'L');

// Sexta fila (formateo de la fecha)
$fecha_registro = date("d/m/Y", strtotime($cliente['fechaSeguimientoInicio']));
$pdf->Cell(50, 10, 'Fecha de Registro:', 1, 0, 'L', true);
$pdf->Cell(0, 10, $fecha_registro, 1, 1, 'L');

$pdf->Ln(10); // Espaciado

// Consultar las mascotas del propietario (mascotas)
$sql_mascotas = "SELECT * FROM mascotas WHERE propietario_id = ? LIMIT 1"; // Limitar a una mascota
$stmt_mascotas = $conn->prepare($sql_mascotas);
$stmt_mascotas->bind_param("i", $id);
$stmt_mascotas->execute();
$result_mascotas = $stmt_mascotas->get_result();

$pdf->SetFont('Arial', 'B', 14);
$pdf->SetTextColor(0, 51, 102);
$pdf->Cell(0, 10, 'Mascota del Propietario:', 0, 1, 'L');
$pdf->Ln(5);

// Tabla de mascota
$pdf->SetFont('Arial', 'B', 12);
$pdf->SetFillColor(200, 200, 200); // Fondo gris claro para encabezados
$pdf->SetTextColor(0, 0, 0); // Texto negro
$pdf->SetDrawColor(200, 200, 200); // Bordes gris claro

$pdf->Cell(50, 10, 'Nombre', 1, 0, 'C', true);
$pdf->Cell(50, 10, 'Especie', 1, 0, 'C', true);
$pdf->Cell(50, 10, 'Raza', 1, 0, 'C', true);
$pdf->Cell(50, 10, 'Fecha de Nacimiento', 1, 1, 'C', true);

// Datos de la mascota
$pdf->SetFont('Arial', '', 12);
$pdf->SetFillColor(240, 240, 240); // Fondo gris claro
$pdf->SetTextColor(0, 0, 0); // Texto negro

if ($result_mascotas->num_rows > 0) {
    $mascota = $result_mascotas->fetch_assoc();
    $pdf->Cell(50, 10, $mascota['nombre'], 1, 0, 'L', true);
    $pdf->Cell(50, 10, $mascota['especie'], 1, 0, 'L', true);
    $pdf->Cell(50, 10, $mascota['raza'], 1, 0, 'L', true);
    $pdf->Cell(50, 10, date("d/m/Y", strtotime($mascota['fechaNacimiento'])), 1, 1, 'L', true);
} else {
    $pdf->Cell(0, 10, 'No hay mascotas registradas.', 1, 1, 'L', true);
}

$pdf->Ln(10); // Espaciado

// Agregar cuadro de Descripción
$pdf->SetFont('Arial', 'B', 12);
$pdf->SetTextColor(0, 51, 102);
$pdf->Cell(0, 10, 'LAGOMORFOS:', 0, 1, 'L');
$pdf->Ln(5);

// Caja de descripción
$pdf->SetFont('Arial', '', 12);
$pdf->SetFillColor(240, 240, 240); // Fondo gris claro
$pdf->SetTextColor(0, 0, 0); // Texto negro

// Consultar la descripción más reciente de la mascota desde la tabla historial_visitas
$sql_historial = "SELECT descripcion FROM historial_visitas WHERE mascota_id = ? ORDER BY fecha_visita DESC LIMIT 1";
$stmt_historial = $conn->prepare($sql_historial);
$stmt_historial->bind_param("i", $mascota['id']);
$stmt_historial->execute();
$result_historial = $stmt_historial->get_result();

// Verificar si existe una descripción
if ($result_historial->num_rows > 0) {
    $descripcion = $result_historial->fetch_assoc()['descripcion'];
} else {
    $descripcion = 'No se proporcionó descripción.';
}

// Agregar el contenido de la descripción al PDF
$pdf->MultiCell(0, 10, $descripcion, 1, 'L', true); // MultiCell permite que el texto se ajuste

$pdf->Ln(10); // Espaciado

// Salida del PDF
$pdf->Output('D', 'tarjeta_presentacion_' . $id . '.pdf'); // Descargar el PDF
$conn->close();
?>

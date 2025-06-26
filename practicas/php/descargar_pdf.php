<?php
// C:\xampp\htdocs\P-5\practicas\php\detalle-propietario.php

// Descargar PDF de los detalles del paciente principal y sus consultas
require('../libs/fpdf/fpdf.php'); // Asegúrate de incluir la librería FPDF correctamente

// --- Configuración de la conexión a la base de datos ---
$dbHost = "srv805.hstgr.io";
$dbUser = "u666383048_clinica";
$dbPass = "9~o0jY:Xw";
$dbName = "u666383048_clinica";
$dbPort = 3306;

// Establecer conexión con la base de datos
$conn = new mysqli($dbHost, $dbUser, $dbPass, $dbName, $dbPort);
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}
$conn->set_charset("utf8mb4");

// Obtener el ID del paciente principal de la URL
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($id <= 0) {
    die("ID de paciente principal no válido.");
}

// --- Consulta del paciente principal (tabla 'pacientes') ---
$sql_paciente_principal = "SELECT id, nombre, direccion, telefono, dni, fechaNacimiento, nacionalidad FROM pacientes WHERE id = ?";
$stmt_paciente_principal = $conn->prepare($sql_paciente_principal);

if ($stmt_paciente_principal === false) {
    die("Error al preparar la consulta del paciente principal: " . $conn->error);
}

$stmt_paciente_principal->bind_param("i", $id);
$stmt_paciente_principal->execute();
$result_paciente_principal = $stmt_paciente_principal->get_result();

if ($result_paciente_principal->num_rows > 0) {
    $paciente_principal = $result_paciente_principal->fetch_assoc();
} else {
    die("Paciente principal no encontrado.");
}
$stmt_paciente_principal->close();

// --- Crear PDF ---
$pdf = new FPDF();
$pdf->AddPage();
$pdf->SetFont('Arial', 'B', 16);

// Encabezado principal
$pdf->SetTextColor(0, 51, 102); // Color azul oscuro
$pdf->Cell(0, 10, 'Detalles del PACIENTE PRINCIPAL', 0, 1, 'C');
$pdf->Ln(10);

// Separador decorativo
$pdf->SetDrawColor(0, 51, 102);
$pdf->SetLineWidth(0.5);
$pdf->Line(10, 30, 200, 30);
$pdf->Ln(5);

// --- Sección de Información del Paciente Principal ---
$pdf->SetFont('Arial', 'B', 14);
$pdf->SetTextColor(0, 51, 102);
$pdf->Cell(0, 10, 'Informacion del Paciente Principal', 0, 1, 'L');
$pdf->Ln(5);

$pdf->SetFont('Arial', 'B', 12);
$pdf->SetFillColor(200, 220, 255); // Color de fondo para encabezado de tabla
$pdf->SetTextColor(0, 51, 102); // Color del texto

// Fila 1: ID y DNI
$pdf->Cell(50, 10, 'ID del Paciente:', 1, 0, 'L', true);
$pdf->Cell(45, 10, $paciente_principal['id'], 1, 0, 'L');
$pdf->Cell(50, 10, 'DNI:', 1, 0, 'L', true);
$pdf->Cell(45, 10, $paciente_principal['dni'], 1, 1, 'L');

// Fila 2: Nombre
$pdf->Cell(50, 10, 'Nombre Completo:', 1, 0, 'L', true);
$pdf->Cell(0, 10, $paciente_principal['nombre'], 1, 1, 'L');

// Fila 3: Dirección
$pdf->Cell(50, 10, 'Direccion:', 1, 0, 'L', true);
$pdf->Cell(0, 10, $paciente_principal['direccion'], 1, 1, 'L');

// Fila 4: Teléfono
$pdf->Cell(50, 10, 'Telefono:', 1, 0, 'L', true);
$pdf->Cell(0, 10, $paciente_principal['telefono'], 1, 1, 'L');

// Fila 5: Fecha de Nacimiento
$pdf->Cell(50, 10, 'Fecha Nacimiento:', 1, 0, 'L', true);
$pdf->Cell(0, 10, date("d/m/Y", strtotime($paciente_principal['fechaNacimiento'])), 1, 1, 'L');

// Fila 6: Nacionalidad
$pdf->Cell(50, 10, 'Nacionalidad:', 1, 0, 'L', true);
$pdf->Cell(0, 10, $paciente_principal['nacionalidad'], 1, 1, 'L');

$pdf->Ln(10); // Espaciado

// --- Sección de Consultas Médicas Asociadas ---
$pdf->SetFont('Arial', 'B', 14);
$pdf->SetTextColor(0, 51, 102);
$pdf->Cell(0, 10, 'Consultas Medicas Asociadas:', 0, 1, 'L');
$pdf->Ln(5);

// Consulta todas las consultas médicas para este paciente principal
$sql_consultas_medicas = "SELECT id, nombre_consulta, diagnostico_breve, sexo, especialidad, fecha_consulta, diagnostico_detallado FROM consultas_medicas WHERE paciente_id = ?";
$stmt_consultas_medicas = $conn->prepare($sql_consultas_medicas);

if ($stmt_consultas_medicas === false) {
    die("Error al preparar la consulta de consultas médicas: " . $conn->error);
}

$stmt_consultas_medicas->bind_param("i", $id);
$stmt_consultas_medicas->execute();
$result_consultas_medicas = $stmt_consultas_medicas->get_result();

if ($result_consultas_medicas->num_rows > 0) {
    $counter = 1;
    while ($consulta = $result_consultas_medicas->fetch_assoc()) {
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->SetTextColor(0, 0, 0);
        $pdf->Cell(0, 10, "Consulta #" . $counter, 0, 1, 'L');
        $pdf->SetFont('Arial', '', 10);
        $pdf->SetFillColor(240, 240, 240); // Fondo gris claro para filas
        $pdf->SetTextColor(0, 0, 0); // Texto negro

        $pdf->Cell(50, 8, 'ID Consulta:', 1, 0, 'L', true);
        $pdf->Cell(45, 8, $consulta['id'], 1, 0, 'L');
        $pdf->Cell(50, 8, 'Nombre de Consulta:', 1, 0, 'L', true);
        $pdf->Cell(45, 8, $consulta['nombre_consulta'], 1, 1, 'L');

        $pdf->Cell(50, 8, 'Diagnostico Breve:', 1, 0, 'L', true);
        $pdf->Cell(45, 8, $consulta['diagnostico_breve'], 1, 0, 'L');
        $pdf->Cell(50, 8, 'Sexo:', 1, 0, 'L', true);
        $pdf->Cell(45, 8, $consulta['sexo'], 1, 1, 'L');

        $pdf->Cell(50, 8, 'Especialidad:', 1, 0, 'L', true);
        $pdf->Cell(45, 8, $consulta['especialidad'], 1, 0, 'L');
        $pdf->Cell(50, 8, 'Fecha Consulta:', 1, 0, 'L', true);
        $pdf->Cell(45, 8, date("d/m/Y", strtotime($consulta['fecha_consulta'])), 1, 1, 'L');

        $pdf->Cell(50, 8, 'Diagnostico Detallado:', 1, 0, 'L', true);
        $pdf->MultiCell(0, 8, $consulta['diagnostico_detallado'], 1, 'L');
        $pdf->Ln(2); // Pequeño espacio entre campos

        // --- Historial de Visitas para esta Consulta ---
        $pdf->SetFont('Arial', 'B', 10);
        $pdf->SetTextColor(0, 51, 102);
        $pdf->Cell(0, 7, 'Historial de Visitas para esta Consulta:', 0, 1, 'L');
        $pdf->SetFont('Arial', '', 9);
        $pdf->SetFillColor(230, 230, 230);

        $sql_visitas_detalle = "SELECT fecha_visita, descripcion FROM visitas_detalle WHERE paciente_id = ? AND consulta_id = ? ORDER BY fecha_visita DESC";
        $stmt_visitas_detalle = $conn->prepare($sql_visitas_detalle);

        if ($stmt_visitas_detalle === false) {
            throw new Exception("Error al preparar la consulta de visitas de detalle: " . $conn->error);
        }

        $stmt_visitas_detalle->bind_param("ii", $paciente_principal['id'], $consulta['id']);
        $stmt_visitas_detalle->execute();
        $result_visitas_detalle = $stmt_visitas_detalle->get_result();

        if ($result_visitas_detalle->num_rows > 0) {
            $pdf->Cell(40, 7, 'Fecha Visita', 1, 0, 'C', true);
            $pdf->Cell(0, 7, 'Descripcion de Visita', 1, 1, 'C', true);
            while ($visita = $result_visitas_detalle->fetch_assoc()) {
                $pdf->Cell(40, 7, date("d/m/Y", strtotime($visita['fecha_visita'])), 1, 0, 'L');
                $pdf->MultiCell(0, 7, $visita['descripcion'], 1, 'L');
            }
        } else {
            $pdf->Cell(0, 7, 'No hay visitas registradas para esta consulta.', 1, 1, 'L', true);
        }
        $stmt_visitas_detalle->close();
        $pdf->Ln(5); // Espacio entre consultas
        $counter++;
    }
} else {
    $pdf->SetFont('Arial', '', 12);
    $pdf->Cell(0, 10, 'No hay consultas médicas registradas para este paciente principal.', 1, 1, 'L', true);
}
$stmt_consultas_medicas->close();

$pdf->Output('D', 'detalles_paciente_principal_' . $id . '.pdf');
$conn->close();
?>
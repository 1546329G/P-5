<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Clínica</title>
    <link rel="stylesheet" type="text/css" href="../css2/index.css">
    <link rel="icon" href="../img/favicon2.ico" type="image/x-icon">
</head>
<body>
<h1 class="databox">Bienvenido al registro de pacientes</h1>
<div class="containertotal">
<!-- Datos del paciente -->
<form class = "formulariocontainer" action="conexion.php" method="POST">
        <div class="paciente-container">
        <h1 class="containerdatas">Datos del Paciente</h1>
            <div class="column">
                <div class="pacios-letra">
                    <label>Nombre :
                    <input type="text" name="nombre" placeholder="Escriba el Nombre" required></label>
                </div>
                <div class="pacios-letra">
                    <label>Apellidos:
                    <input type="text" name="apellidos" placeholder="Escriba los Apellidos" required></label>
                </div>
                <div class="pacios-letra">
                    <label>DNI:
                    <input type="text" name="dni" placeholder="Número de Identificación" required></label>
                </div>
                <div class="pacios-letra">
                    <label>Fecha de Nacimiento:
                    <input type="date" name="fecha_nacimiento" required></label>
                </div>
                <div class="pacios-letra">
                    <label>Dirección:
                    <input type="text" name="direccion" placeholder="Ingrese su dirección"></label>
                </div>
                <div class="pacios-letra">
                    <label>Teléfono:
                    <input type="text" name="telefono" placeholder="Número de Teléfono" required></label>
                </div>
            </div>
            <!-- Datos médicos -->
            <div class="column">
            <div class="pacios-letra">
                   <label for="sexo">Sexo:</label>
                   <select name="sexo" id="sexo" required>
                   <option value="Masculino">Masculino</option>
                   <option value="Femenino">Femenino</option>
                   <option value="Otro">Otro</option>
                   </select>
            </div>
                <div class="pacios-letra">
                    <label>Diagnóstico:
                    <input type="text" name="diagnostico" placeholder="Escriba el Diagnóstico" required></label>
                </div>
                <div class="pacios-letra">
                    <label>Doctor Asignado:
                    <input type="text" name="id_doctor" placeholder="Ingrese nombre del Doctor" required></label>
                </div>
                <div class="pacios-letra">
                    <label>Fecha de Seguimiento Inicio:
                    <input type="date" name="fecha_seguimiento_inicio"></label>
                </div>
                <div class="pacios-letra">
                    <label>Fecha de Seguimiento Fin:
                    <input type="date" name="fecha_seguimiento_fin"></label>
                    <button type="button" id="btndescripcion">Agregar Descripción</button>
        <div id="notas"></div> 
        <div class="submit-group">
            <button type="submit">Enviar datos</button>
            <input type="hidden" name="descripcion" id="descripcion">
        </div>
    <script src="../js/index.js"></script>
                </div>
            </div>
        </div>
    </form>
</body>
    </div>
</div>
</body>
</html>

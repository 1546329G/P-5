<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Historia Clínica Veterinaria</title>
    <link rel="stylesheet" type="text/css" href="../css2/indexx.css">
    <link rel="icon" href="../img/favicon2.ico" type="image/x-icon">
</head>
<body>
    
    <form action="conexion.php" method="POST">
        <h1 class="databox">Datos del paciente</h1>
        <div class="cliente-container">
            <div class="column">
                <div class="pacios-letra">
                    <label>Nombres del doctor tratante:
                    <input type="text" name="doctor" placeholder="Escriba los nombres del doctor" required></label>
                </div>
                
                <div class="pacios-letra">
                    <label>Correo electrónico del paciente:
                    <input type="email" name="email" placeholder="Escriba el correo electrónico del paciente"></label>
                </div>
                <div class="pacios-letra">
                    <label>Dirección del paciente:
                    <input type="text" name="direccion" placeholder="Escriba la dirección física del paciente"></label>
                </div>
                <div class="pacios-letra">
                    <label>Teléfono del paciente:
                    <input type="text" name="telefono" placeholder="Escriba el Teléfono del paciente" required></label>
                </div>
            </div>
            <div class="column">
                <div class="pacios-letra">
                    <label>Nombres del paciente:
                    <input type="text" name="nombre" placeholder="Escriba los nombres del paciente" required></label>
                </div>
                
                <div class="pacios-letra">
                    <label>Fecha de nacimiento del paciente:
                    <input type="date" id="fechaNacimiento" name="fechaNacimiento" required></label>
                </div>
                <div class="pacios-letra">
                    <label>DNI / ID:
                    <input type="text" name="dni" placeholder="Escriba el DNI o ID del paciente" required></label>
                </div>
            </div>
        </div>

    <div class="especies-container">
        <label for="nacionalidad"><b>Nacionalidad:</b></label><br><br>
        <label for="peruano">Peruano</label>
        <input type="radio" id="peruano" name="nacionalidad" value="Peruano"><br><br>
        <label for="extranjero">Extranjero</label>
        <input type="radio" id="extranjero" name="nacionalidad" value="Extranjero">
    </div><br>

    <div class="sexos-container">
        <label><b>Sexo:</b></label><br><br>
        <input type="radio" name="sexo" id="macho" value="masculino" required>
        <label for="macho">Hombre</label><br><br>
        <input type="radio" name="sexo" id="hembra" value="femenino" required>
        <label for="hembra">Mujer</label>
    </div>

    <div class="raza-color-container">
        <div class="raza-container">
            <label>Diagnóstico <input type="text" name="diagnostico" placeholder="Diagnóstico Presuntivo" required></label>
        </div>
        <div class="color-container">
            <label>Especialidad Médica: <input type="text" name="especialidad" placeholder="Especialidad " required></label>
        </div>
    </div>

    <div class="fechas-container">
        <div class="fecha-inicio-container">
            <label for="fechaSeguimientoInicio">Fecha de inicio de tratamiento:</label>
            <input type="date" id="fechaSeguimientoInicio" name="fechaSeguimientoInicio">
        </div>
    </div>

        <button type="button" id="btndescripcion">Agregar Descripción</button>
        <div id="notas"></div> 
        <div class="submit-group">
            <button type="submit">Enviar datos</button>
            <input type="hidden" name="descripcion" id="descripcion">
        </div>
    </form>
    <script src="../js/index.js"></script>
</body>
</html>
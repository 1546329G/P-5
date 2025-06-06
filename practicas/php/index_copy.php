<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Historia Clínica Veterinaria</title>
    <link rel="stylesheet" type="text/css" href="../css/index.css">
</head>
<body>
    <form action="conexion.php" method="POST">
 
    <div class="cliente-container">
            <!-- Columna 1 -->
            <div class="column">
                <div class="pacios-letra">
                    <label>Nombres del doctor tratante:
                    <input type="text" name="propietario" placeholder="Escriba los nombres del doctor" required></label>
                </div>
                <div class="pacios-letra">
                    <label>Apellidos del doctor tratante:
                    <input type="text" name="propietario" placeholder="Escriba los apellidos del doctor" required></label>
                </div>
                <div class="pacios-letra">
                    <label>Correo electronico del paciente:
                    <input type="text" name="direccion" placeholder="Escriba el correo electronico del paciente"></label>
                </div>
                <div class="pacios-letra">
                    <label>Teléfono del paciente:
                    <input type="text" name="telefono" placeholder="Escriba el Teléfono del paciente" required></label>
                </div>
            </div>
            <!-- Columna 2 -->
            <div class="column">
                <div class="pacios-letra">
                    <label>Nombres del paciente:
                    <input type="text" name="paciente" placeholder="Escriba los nombres del paciente" required></label>
                </div>
                <div class="pacios-letra">
                    <label>Apellidos del paciente:
                    <input type="text" name="paciente" placeholder="Escriba los apellidos del paciente" required></label>
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


    <!-- Contenedor para especies (izquierda) -->
    <div class="especies-container">
    <label for="Sexo"><b>Nacionalidad:</b></label><br><br>
        <label for="Canino">Peruano</label>
        <input type="radio" id="Canino" name="especie" value="Canino"><br><br>
        <label for="Felino">Extranjero</label>
        <input type="radio" id="Felino" name="especie" value="Felino">
        <!--
        <label for="Aves">Aves</label>
        <input type="radio" id="Aves" name="especie" value="Aves">
        <label for="Lagomorfos">Lagomorfos</label>
        <input type="radio" id="Lagomorfos" name="especie" value="Lagomorfos">
        <label for="otros">Otros</label>
        <input type="radio" id="otros" name="especie" value="otros">-->
    </div><br>

    <!-- Contenedor para sexos (derecha) -->
    <div class="sexos-container">
    <label><b>Sexo:</b></label><br><br>
        <input type="radio" name="sexo" id="macho" value="macho" required>
        <label for="macho">Hombre</label><br><br>
        <input type="radio" name="sexo" id="hembra" value="hembra">
        <label for="hembra">Mujer</label>


     </div>


<!-- Contenedor para Raza y Color -->
<div class="raza-color-container">
    <!-- Raza a la izquierda -->
    <div class="raza-container">
        <label>Diagnóstico <input type="text" name="raza" placeholder="Diagnóstico Presuntivo" required></label>
    </div>
    
    <!-- Color a la derecha -->
    <div class="color-container">
        <label>Especialidad Médica: <input type="text" name="color" placeholder="Especialidad " required></label>
    </div>
</div>


<!-- Contenedor para fechas de seguimiento -->
<div class="fechas-container">
    <!-- Fecha de inicio a la izquierda -->
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

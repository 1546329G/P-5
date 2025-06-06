<?php
// Iniciar sesión
session_start();

// Verificar si el usuario está autenticado
if (!isset($_SESSION['autenticado']) || $_SESSION['autenticado'] !== true) {
    // Si no está autenticado, redirigir al login
    header("Location: ../index.html");
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css2/ventanasss.css">
    <link rel="stylesheet" href="../css/a.css">
    <link rel="stylesheet" href="../css/paginador.css">
    <link rel="icon" href="../img/favicon2.ico" type="image/x-icon">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700&family=Quicksand:wght@400;600&display=swap" rel="stylesheet">

    <title>Gestión de Pacientes</title>
  
</head>
<body>
<div class="login-wrapper"> 
<div class="exchange-box">

<h1>Gestión de Pacientes</h1>

<!-- Menú desplegable -->
<div class="dropdown">
  <button class="dropbtn">Menú</button>
  <div class="dropdown-content">
    <a href="../index.html">Cerrar sesión</a>
    <a href="https://www.facebook.com/gerson.gomez.75">Facebook</a>
    <a href="../index.html">Website</a>
    <!-- Aquí puedes agregar más opciones en el futuro -->
    <!-- <a href="#">Otra opción</a> -->
  </div>
</div>


<!-- Mostrar usuario conectado 
<div class="user-info">
    Bienvenido, 
    <a href="logout.php">Cerrar sesión</a>
</div>-->

<!-- Botones principales -->
<div class="button-container">
    <div class="button-group">
        <button onclick="window.location.href='index.php'">Registrar Paciente</button>
        
    </div>
    
    <div class="button-group">
        <button onclick="mostrarBusqueda()">Buscar Paciente</button>
        
    </div>

    <div class="button-group">
        <button onclick="window.location.href='ver-clientes.php'">Mostrar Paciente</button>
        
    </div>
</div>

<div class="search" id="searchSection" style="display: none; margin-top: 20px;">
    <input type="text" id="cliente_id" placeholder="ID del cliente">
    <button type="button" onclick="buscarPaciente()">Buscar</button>
</div>

<!-- Modal -->
<div id="modal-overlay" style="display: none;"></div>
<div id="modal" style="display: none;">
    <span class="close-btn" onclick="cerrarModal()">×</span>
    <div id="modal-content"></div> <!-- Aquí se cargará el contenido dinámico -->
</div><br><br><br>

<!-- Imagenes -->
<div class="data">
    <img src="../img/data5.jpg" class="imagenes" alt="Imagen 1">
    <img src="../img/data1.jpg" class="imagenes" alt="Imagen 2">
    <img src="../img/data7.jpg" class="imagenes" alt="Imagen 3">
</div><br><br><br>

</div>
</div>

<script>
// Espera a que el DOM esté completamente cargado
document.addEventListener("DOMContentLoaded", function() {
    const images = document.querySelectorAll('.imagenes'); // Selecciona todas las imágenes con la clase 'imagenes'
    let currentIndex = 0;

    // Muestra la primera imagen
    images[currentIndex].classList.add('active');

    // Cambia de imagen cada 5 segundos
    setInterval(() => {
        // Elimina la clase 'active' de la imagen actual
        images[currentIndex].classList.remove('active');

        // Actualiza el índice de la imagen actual (pasando al siguiente)
        currentIndex = (currentIndex + 1) % images.length;

        // Añade la clase 'active' a la nueva imagen
        images[currentIndex].classList.add('active');
    }, 5000); // Cambia de imagen cada 5000 ms (5 segundos)
});



function mostrarBusqueda() {
    document.getElementById('searchSection').style.display = 'block';
}

function buscarPaciente() {
    const cliente_id = document.getElementById('cliente_id').value;
    const modal = document.getElementById('modal');
    const modalContent = document.getElementById('modal-content');
    const overlay = document.getElementById('modal-overlay');

    if (cliente_id.trim() === '') {
        alert('Por favor, ingresa un ID de cliente.');
        return;
    }
    const xhr = new XMLHttpRequest();
    xhr.open('POST', 'buscar-paciente.php', true);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    xhr.onload = function () {
        if (xhr.status === 200) {
            modalContent.innerHTML = xhr.responseText; // Cargar contenido dinámico
            modal.style.display = 'block';
            overlay.style.display = 'block';
        } else {
            modalContent.innerHTML = '<p style="color: red;">Error al buscar el cliente.</p>';
        }
    };
    xhr.send('cliente_id=' + encodeURIComponent(cliente_id) + '&pagina=1');
}

function cerrarModal() {
    document.getElementById('modal').style.display = 'none';
    document.getElementById('modal-overlay').style.display = 'none';
}

function cargarPagina(pagina) {
    const cliente_id = document.getElementById('cliente_id').value;
    const modalContent = document.getElementById('modal-content');
    const xhr = new XMLHttpRequest();
    xhr.open('POST', 'buscar-paciente.php', true);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    xhr.onload = function () {
        if (xhr.status === 200) {
            modalContent.innerHTML = xhr.responseText;
        }
    };
    xhr.send('cliente_id=' + encodeURIComponent(cliente_id) + '&pagina=' + pagina);
}
</script>

</body>
</html>
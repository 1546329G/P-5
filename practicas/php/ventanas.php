<?php
session_start();

if (!isset($_SESSION['autenticado']) || $_SESSION['autenticado'] !== true) {
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
    <link rel="stylesheet" href="../css/modal-busqueda.css">
    <link rel="stylesheet" href="../css/paginador.css">
    <link rel="icon" href="../img/favicon2.ico" type="image/x-icon">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700&family=Quicksand:wght@400;600&display=swap" rel="stylesheet">
    <title>Gestión de Pacientes</title>
</head>
<body>
<div class="login-wrapper"> 
<div class="exchange-box">

<h1>Gestión de Pacientes</h1>

<div class="dropdown">
    <button class="dropbtn">Menú</button>
    <div class="dropdown-content">
        <a href="../index.html">Cerrar sesión</a>
        <a href="https://www.facebook.com/gerson.gomez.75">Facebook</a>
        <a href="../index.html">Website</a>
    </div>
</div>

<div class="button-container">
    <div class="button-group">
        <button onclick="window.location.href='index.php'">Registrar Paciente</button>
    </div>
    <div class="button-group">
        <button onclick="mostrarBusqueda()">Buscar Paciente</button> 
    </div>
    <div class="button-group">
        <button onclick="window.location.href='ver-clientes.php'">Mostrar Pacientes</button>
    </div>
</div>

<div class="search" id="searchSection" style="display: none; margin-top: 20px;">
<input type="text" id="busqueda" placeholder="ID o Nombre del paciente">
<button type="button" onclick="buscarPaciente()">Buscar</button>
</div>

<div id="modal-overlay" style="display: none;"></div>
<div id="modal" style="display: none;">
    <span class="close-btn" onclick="cerrarModal()">×</span>
    <div id="modal-content"></div>
</div><br><br><br>

<div class="data">
    <img src="../img/data5.jpg" class="imagenes" alt="Imagen 1">
    <img src="../img/data1.jpg" class="imagenes" alt="Imagen 2">
    <img src="../img/data7.jpg" class="imagenes" alt="Imagen 3">
</div><br><br><br>

</div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function() {
    const images = document.querySelectorAll('.imagenes');
    let currentIndex = 0;

    images[currentIndex].classList.add('active');

    setInterval(() => {
        images[currentIndex].classList.remove('active');
        currentIndex = (currentIndex + 1) % images.length;
        images[currentIndex].classList.add('active');
    }, 5000);
});

function mostrarBusqueda() {
    document.getElementById('searchSection').style.display = 'block';
}

function buscarPaciente() {
    const input = document.getElementById('busqueda').value.trim();
    const modal = document.getElementById('modal');
    const modalContent = document.getElementById('modal-content');
    const overlay = document.getElementById('modal-overlay');

    if (input === '') {
        alert('Por favor, ingresa un ID o nombre del paciente.');
        return;
    }

    const xhr = new XMLHttpRequest();
    xhr.open('POST', 'buscar-paciente.php', true);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

    let parametros = '';
    if (!isNaN(input)) {
        parametros = 'paciente_id=' + encodeURIComponent(input); // Cambiado a paciente_id
    } else {
        parametros = 'nombre=' + encodeURIComponent(input);
    }

    parametros += '&pagina=1';

    xhr.onload = function () {
        if (xhr.status === 200) {
            modalContent.innerHTML = xhr.responseText;
            modal.style.display = 'block';
            overlay.style.display = 'block';
        } else {
            modalContent.innerHTML = '<p style="color: red;">Error al buscar el paciente.</p>';
        }
    };
    xhr.send(parametros);
}

function cerrarModal() {
    document.getElementById('modal').style.display = 'none';
    document.getElementById('modal-overlay').style.display = 'none';
}

function cargarPagina(pagina) {
    const input = document.getElementById('busqueda').value.trim();
    const modalContent = document.getElementById('modal-content');
    const xhr = new XMLHttpRequest();
    xhr.open('POST', 'buscar-paciente.php', true);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

    let parametros = '';
    if (!isNaN(input)) {
        parametros = 'paciente_id=' + encodeURIComponent(input); // Cambiado a paciente_id
    } else {
        parametros = 'nombre=' + encodeURIComponent(input);
    }

    parametros += '&pagina=' + pagina;

    xhr.onload = function () {
        if (xhr.status === 200) {
            modalContent.innerHTML = xhr.responseText;
        }
    };
    xhr.send(parametros);
}
</script>

</body>
</html>
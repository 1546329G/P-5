// procesar_seguimiento.js

$(document).ready(function() {

    // --- Lógica para el modal de Detalles de Consulta ---
    // (Este evento debería estar ya en tu JS principal o donde manejes los botones "Más información")
    // Asegúrate de que el botón que abre el modal de detalles de consulta tenga la clase 'btn-mas-informacion'
    // y los data-attributes adecuados (data-id, data-nombre, etc.)
    $(document).on('click', '.btn-mas-informacion', function() {
        var consultaId = $(this).data('id'); 
        
        // Rellenar los demás campos de la consulta en el modal de detalles
        // Asegúrate de que estos data-attributes correspondan a los que generas en PHP
        $('#detalle-nombre-consulta').text($(this).data('nombre'));
        $('#detalle-diagnostico-breve').text($(this).data('diagbreve'));
        $('#detalle-sexo-consulta').text($(this).data('sexo'));
        $('#detalle-especialidad-consulta').text($(this).data('especialidad'));
        $('#detalle-fecha-consulta').text($(this).data('fecha'));
        $('#detalle-diagnostico-detallado').text($(this).data('diagdetallado'));

        // Pasa el ID de la consulta al span de visualización y al input hidden del formulario de seguimiento
        $('#detallesConsultaIdSpan').text(consultaId); 
        $('#consultaIdSeguimientoInput').val(consultaId); 

        // Limpiar seguimientos anteriores y mostrar mensaje de "cargando" o "no hay"
        $('#detalle-historial-visitas').empty();
        $('#noSeguimientosMessage').show(); // Mostrar inicialmente, se ocultará si hay seguimientos

        // LLAMADA AJAX para cargar los seguimientos existentes para esta consulta
        cargarSeguimientos(consultaId);

        // Mostrar el modal de detalles de la consulta
        $('#detallesConsultaModalOverlay').fadeIn(); 
    });

    // Cerrar el modal de detalles de consulta
    $('#closeDetallesConsultaModalBtn').on('click', function() {
        $('#detallesConsultaModalOverlay').fadeOut();
    });


    // --- Lógica para el modal de Añadir Seguimiento ---

    // Abrir el modal de Añadir Seguimiento al hacer clic en el botón dentro del modal de Detalles
    $('#addSeguimientoBtn').on('click', function() {
        // Ocultar el modal de detalles y luego mostrar el modal de añadir seguimiento
        $('#detallesConsultaModalOverlay').fadeOut(function() {
            $('#addSeguimientoModalOverlay').fadeIn();
            // Opcional: enfocar el primer campo del formulario de seguimiento
            $('#fechaSeguimiento').focus(); 
        });
    });

    // Cerrar el modal de Añadir Seguimiento (botones de cerrar y cancelar)
    $('#closeAddSeguimientoModalBtn, #cancelAddSeguimientoBtn').on('click', function() {
        $('#addSeguimientoModalOverlay').fadeOut(function() {
            // Reabrir el modal de detalles de consulta después de cerrar el de añadir seguimiento
            var currentConsultaId = $('#consultaIdSeguimientoInput').val();
            if (currentConsultaId) { 
                $('#detallesConsultaModalOverlay').fadeIn(); 
            }
        });
        $('#formAddSeguimiento')[0].reset(); // Limpiar el formulario después de cerrarlo
    });


    // --- Función para cargar los seguimientos existentes (Vía AJAX) ---
    // Esta función hace una petición GET a procesar_seguimientos.php
    function cargarSeguimientos(consultaId) {
        $.ajax({
            url: 'procesar_seguimientos.php', // Apunta al script PHP único
            type: 'GET',
            data: { consulta_id: consultaId }, // Envía el ID de la consulta
            dataType: 'json', 
            success: function(seguimientos) {
                var $historialList = $('#detalle-historial-visitas');
                $historialList.empty(); // Limpiar la lista antes de añadir nuevos elementos
                
                if (seguimientos && seguimientos.length > 0) {
                    $('#noSeguimientosMessage').hide(); // Ocultar el mensaje si hay seguimientos
                    $.each(seguimientos, function(index, seguimiento) {
                        var listItem = `
                            <li class="list-group-item">
                                <strong>Fecha:</strong> ${seguimiento.fecha_seguimiento} <br>
                                <strong>Descripción:</strong> ${seguimiento.descripcion_seguimiento} <br>
                                ${seguimiento.doctor_seguimiento ? '<strong>Doctor:</strong> ' + seguimiento.doctor_seguimiento + '<br>' : ''}
                                ${seguimiento.observaciones_adicionales ? '<strong>Notas:</strong> ' + seguimiento.observaciones_adicionales + '<br>' : ''}
                            </li>
                        `;
                        $historialList.append(listItem);
                    });
                } else {
                    $('#noSeguimientosMessage').show(); // Mostrar el mensaje si no hay seguimientos
                }
            },
            error: function(xhr, status, error) {
                console.error("Error al cargar seguimientos:", xhr.responseText);
                $('#detalle-historial-visitas').empty().append('<li class="list-group-item text-danger">Error al cargar el historial de seguimientos.</li>');
                $('#noSeguimientosMessage').hide(); 
            }
        });
    }


    // --- Envío del Formulario de Añadir Seguimiento (Vía AJAX) ---
    // Esta función hace una petición POST a procesar_seguimientos.php
    $('#formAddSeguimiento').submit(function(event) {
        event.preventDefault(); // Prevenir el envío normal del formulario

        var formData = $(this).serialize(); // Serializa todos los campos del formulario
        console.log("Datos del nuevo seguimiento a enviar:", formData);

        $.ajax({
            url: 'procesar_seguimientos.php', // Apunta al script PHP único
            type: 'POST',
            data: formData,
            dataType: 'json', // Esperamos una respuesta JSON del servidor
            success: function(response) {
                console.log("Respuesta del servidor al añadir seguimiento:", response);
                if (response.success) {
                    alert(response.message);
                    $('#addSeguimientoModalOverlay').fadeOut(); // Cerrar el modal de añadir seguimiento
                    $('#formAddSeguimiento')[0].reset(); // Limpiar el formulario
                    
                    // Recargar la lista de seguimientos en el modal de detalles para mostrar el nuevo
                    var currentConsultaId = $('#consultaIdSeguimientoInput').val();
                    cargarSeguimientos(currentConsultaId); 

                    // Volver a mostrar el modal de detalles de consulta
                    $('#detallesConsultaModalOverlay').fadeIn(); 

                } else {
                    alert("Error al registrar el seguimiento: " + response.message);
                }
            },
            error: function(xhr, status, error) {
                console.error("Error en la petición AJAX al añadir seguimiento:");
                console.error("XHR:", xhr);
                console.error("Status:", status);
                console.error("Error:", error);
                alert("Error al registrar el seguimiento. Consulta la consola del navegador para más detalles.");
            }
        });
    });

}); // Fin de $(document).ready()
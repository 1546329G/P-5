// Archivo: detalle-propietario.js

/**
 * Función de utilidad para obtener elementos del DOM de forma segura y con log de advertencia.
 * @param {string} id El ID del elemento.
 * @param {string} name Un nombre descriptivo para el elemento (para los logs).
 * @returns {HTMLElement | null} El elemento encontrado o null si no existe.
 */
function getElement(id, name) {
    const element = document.getElementById(id);
    if (!element) {
        console.warn(`DOM: Elemento con ID '${id}' (${name}) NO encontrado. La funcionalidad asociada podría no activarse.`);
    } else {
        console.log(`DOM: Elemento '${id}' (${name}) encontrado.`);
    }
    return element;
}

console.log("Inicializando referencias de elementos del DOM...");

// 1. Agrupación de selectores DOM (IDs actualizados para coincidir con tu HTML)
const DOM = {
    // Pestañas
    tabButtons: document.querySelectorAll('.tab-button'),
    tabContents: document.querySelectorAll('.tab-content'),

    // Modal de AGREGAR/EDITAR Consulta (ahora se llama consultaModalOverlay en tu HTML)
    consultaModalOverlay: getElement('consultaModalOverlay', 'Superposición de modal de consulta'), // ID corregido
    closeConsultaModalBtn: getElement('closeConsultaModalBtn', 'Botón cerrar modal de consulta'), // ID corregido
    consultaModalTitle: getElement('consultaModalTitle', 'Título modal de consulta'), // ID corregido
    consultaFormActionType: getElement('consulta_form_action_type', 'Campo oculto tipo acción consulta'), // ID corregido
    consultaIdEdit: getElement('consulta_id_edit', 'Campo oculto ID edición consulta'), // ID corregido
    pacienteIdInput: getElement('pacienteIdInput', 'Campo oculto ID paciente'), // ID corregido (antes propietarioIdInput)
    consultaNombreInput: getElement('consulta_nombre_input', 'Input nombre consulta'), // ID corregido
    consultaDiagnosticoBreveInput: getElement('consulta_diagnostico_breve_input', 'Input diagnóstico breve consulta'), // ID corregido
    consultaSexoInput: getElement('consulta_sexo_input', 'Input sexo consulta'), // ID corregido
    consultaEspecialidadInput: getElement('consulta_especialidad_input', 'Input especialidad consulta'), // ID corregido
    consultaFechaConsultaInput: getElement('consulta_fecha_input', 'Input fecha consulta'), // ID corregido
    consultaDiagnosticoDetalladoInput: getElement('consulta_diagnostico_detallado_input', 'Input diagnóstico detallado consulta'), // ID corregido
    submitConsultaModalBtn: getElement('submitConsultaModalBtn', 'Botón enviar modal consulta'), // ID corregido
    consultaForm: getElement('consultaForm', 'Formulario de consulta'), // ID corregido
    // No hay un botón directo 'openAddMascotaModalBtn' con ese ID, se usa la pestaña 'agregar-consulta-tab' para el formulario directo
    // Pero si quieres un botón para ABRIR EL MODAL DE AGREGAR DESDE OTRA PARTE, sí necesitas uno con un ID.
    // Tu HTML actual tiene la pestaña "Agregar Consulta" que muestra un formulario directo, no un modal flotante.
    // Si la funcionalidad deseada es que el botón "Agregar Nueva Consulta" *dentro de la pestaña "Historial de Consultas Médicas"* abra el modal,
    // ese botón necesitaría la clase o ID que le permita al JS identificarlo como tal.
    // Por ahora, asumimos que el modal se usa para editar y la pestaña para agregar directamente.
    // Si quieres un botón específico para abrir el modal flotante de añadir consulta, deberías añadirle un ID como:
    // <button id="openAddConsultaModalBtn" class="btn btn-success"><i class="fas fa-plus"></i> Agregar Nueva Consulta (Modal)</button>
    // Y luego referenciarlo aquí:
    // openAddConsultaModalBtn: getElement('openAddConsultaModalBtn', 'Botón para abrir modal de agregar consulta'),


    // Modal de Detalles de Consulta Específica
    detallesConsultaModalOverlay: getElement('detallesConsultaModalOverlay', 'Superposición modal detalles consulta'),
    closeDetallesConsultaModalBtn: getElement('closeDetallesConsultaModalBtn', 'Botón cerrar modal detalles consulta'),
    detallesConsultaIdSpan: getElement('detallesConsultaIdSpan', 'Span ID detalles consulta'), // ID corregido
    detallesConsultaNombreSpan: getElement('detalle-nombre-consulta', 'Span nombre detalles consulta'), // ID corregido
    detallesConsultaDiagnosticoBreveSpan: getElement('detalle-diagnostico-breve', 'Span diagnóstico breve detalles consulta'), // ID corregido
    detallesConsultaSexoSpan: getElement('detalle-sexo-consulta', 'Span sexo detalles consulta'), // ID corregido
    detallesConsultaEspecialidadSpan: getElement('detalle-especialidad-consulta', 'Span especialidad detalles consulta'), // ID corregido
    detallesConsultaFechaConsultaSpan: getElement('detalle-fecha-consulta', 'Span fecha consulta detalles consulta'), // ID corregido
    detallesConsultaDiagnosticoDetalladoSpan: getElement('detalle-diagnostico-detallado', 'Span diagnóstico detallado detalles consulta'), // ID corregido
    editThisConsultaButton: getElement('editThisConsultaButton', 'Botón editar desde detalles consulta'), // ID corregido (antes editThisMascotaButton)

    // Modal de Edición de Paciente Principal
    openEditPacienteModalBtn: getElement('openEditPacienteModalBtn', 'Botón abrir modal edición paciente principal'),
    editPacientePrincipalModalOverlay: getElement('editPacientePrincipalModalOverlay', 'Superposición modal edición paciente principal'),
    closeEditPacientePrincipalModalBtn: getElement('closeEditPacientePrincipalModalBtn', 'Botón cerrar modal edición paciente principal'),
    formEditarClientePrincipal: getElement('formEditarPacientePrincipal', 'Formulario edición paciente principal'), // ID corregido
    clienteIdEdit: null, // Este ID no está en tu HTML, lo manejaremos de otra forma o necesitarás agregarlo
    clienteNombreInput: getElement('pacienteNombreInput', 'Input nombre paciente principal'), // ID corregido
    clienteDniInput: getElement('pacienteDniInput', 'Input DNI paciente principal'), // ID corregido
    clienteDireccionInput: getElement('pacienteDireccionInput', 'Input dirección paciente principal'), // ID corregido
    clienteTelefonoInput: getElement('pacienteTelefonoInput', 'Input teléfono paciente principal'), // ID corregido
    clienteFechaNacimientoInput: getElement('pacienteFechaNacimientoInput', 'Input fecha nacimiento paciente principal'), // ID corregido
    clienteNacionalidadInput: getElement('pacienteNacionalidadInput', 'Input nacionalidad paciente principal'), // ID corregido
    clienteSexoInput: getElement('pacienteSexoInput', 'Input sexo paciente principal'), // Nuevo: Agregado para el sexo del paciente principal
    clienteFechaSeguimientoInicioInput: getElement('pacienteFechaSeguimientoInicioInput', 'Input fecha seguimiento inicio paciente principal'), // Nuevo: Agregado
    clienteFormActionType: null, // Este campo oculto no tiene un ID en tu HTML, se maneja directamente en el formulario
    submitEditClientePrincipalBtn: getElement('submitEditPacientePrincipalBtn', 'Botón enviar edición paciente principal'), // ID corregido

    // Botón de descarga de tarjeta
    descargarTarjetaBtn: getElement('descargarTarjetaBtn', 'Botón descargar tarjeta'),
    tarjetaParaDescargar: getElement('tarjeta-para-descargar', 'Elemento tarjeta para descargar'),
};

console.log("Variables del DOM inicializadas.");

// 2. Manejador de Modales
const ModalManager = {
    /**
     * Abre un modal.
     * @param {HTMLElement} modalOverlay El elemento de la superposición del modal.
     */
    open: function(modalOverlay) {
        if (modalOverlay) {
            modalOverlay.classList.add('active');
            console.log(`ModalManager: Modal '${modalOverlay.id}' abierto.`);
        } else {
            console.warn("ModalManager: Se intentó abrir un modal nulo.");
        }
    },

    /**
     * Cierra un modal.
     * @param {HTMLElement} modalOverlay El elemento de la superposición del modal.
     */
    close: function(modalOverlay) {
        if (modalOverlay) {
            modalOverlay.classList.remove('active');
            console.log(`ModalManager: Modal '${modalOverlay.id}' cerrado.`);
        } else {
            console.warn("ModalManager: Se intentó cerrar un modal nulo.");
        }
    },

    /**
     * Resetea y prepara los campos del formulario de consulta.
     * @param {Object} [consultaData=null] Datos para pre-rellenar el formulario en modo edición.
     */
    _resetAndPrepareConsultaForm: function(consultaData = null) { // Renombrado para consistencia
        DOM.consultaForm?.reset(); // Usa encadenamiento opcional
        console.log("ConsultaForm: Formulario de consulta reseteado.");

        DOM.consultaModalTitle.textContent = consultaData ? `Editar Consulta (ID: ${consultaData.id})` : "Agregar Nueva Consulta";
        DOM.consultaFormActionType.value = consultaData ? 'edit_mascota' : 'agregar_mascota'; // 'edit_mascota' para PHP
        DOM.consultaIdEdit.value = consultaData ? consultaData.id : '';
        DOM.submitConsultaModalBtn.textContent = consultaData ? 'Guardar Cambios de Consulta' : 'Guardar Nueva Consulta';

        if (consultaData) {
            DOM.consultaNombreInput.value = consultaData.nombre_consulta ?? '';
            DOM.consultaDiagnosticoBreveInput.value = consultaData.diagnostico_breve ?? '';
            DOM.consultaSexoInput.value = consultaData.sexo ?? 'masculino';
            DOM.consultaEspecialidadInput.value = consultaData.especialidad ?? '';
            DOM.consultaFechaConsultaInput.value = consultaData.fecha_consulta ?? '';
            DOM.consultaDiagnosticoDetalladoInput.value = consultaData.diagnostico_detallado ?? '';
            console.log("ConsultaForm: Campos cargados con datos de edición.");
        } else {
            // Valores por defecto para nueva consulta
            DOM.consultaNombreInput.value = '';
            DOM.consultaDiagnosticoBreveInput.value = '';
            DOM.consultaSexoInput.value = 'masculino'; // Default masculino
            DOM.consultaEspecialidadInput.value = '';
            DOM.consultaFechaConsultaInput.value = '';
            DOM.consultaDiagnosticoDetalladoInput.value = '';
            console.log("ConsultaForm: Campos limpiados/valor por defecto.");
        }
        console.log(`ConsultaForm: Tipo de acción configurado a '${DOM.consultaFormActionType.value}'.`);
    },

    /**
     * Abre el modal para agregar una nueva consulta.
     */
    openAddConsultaModal: function() { // Renombrado para consistencia
        console.log("ModalManager: Abriendo modal para agregar nueva consulta.");
        ModalManager.close(DOM.detallesConsultaModalOverlay);
        ModalManager.close(DOM.editPacientePrincipalModalOverlay);
        this._resetAndPrepareConsultaForm();
        ModalManager.open(DOM.consultaModalOverlay); // ID corregido
        console.log("ModalManager: openAddConsultaModal finalizada.");
    },

    /**
     * Abre el modal para editar una consulta existente.
     * @param {Object} consultaData Datos de la consulta a editar.
     */
    openEditConsultaModal: function(consultaData) { // Renombrado para consistencia
        console.log("ModalManager: Abriendo modal para editar consulta. Datos:", consultaData);
        ModalManager.close(DOM.detallesConsultaModalOverlay);
        ModalManager.close(DOM.editPacientePrincipalModalOverlay);
        this._resetAndPrepareConsultaForm(consultaData);
        ModalManager.open(DOM.consultaModalOverlay); // ID corregido
        console.log("ModalManager: openEditConsultaModal finalizada.");
    },

    /**
     * Abre el modal para ver los detalles de una consulta.
     * @param {Object} consultaData Datos de la consulta a mostrar.
     */
    openDetallesConsultaModal: function(consultaData) {
        console.log("ModalManager: Abriendo modal de detalles. Datos:", consultaData);
        // Cierre de otros modales, asegurándose de que existan antes de intentar cerrar
        if(DOM.consultaModalOverlay) ModalManager.close(DOM.consultaModalOverlay);
        if(DOM.editPacientePrincipalModalOverlay) ModalManager.close(DOM.editPacientePrincipalModalOverlay);

        // Asegúrate de que los spans existen antes de intentar ponerles texto
        if (DOM.detallesConsultaIdSpan) DOM.detallesConsultaIdSpan.textContent = consultaData.id ?? '';
        if (DOM.detallesConsultaNombreSpan) DOM.detallesConsultaNombreSpan.textContent = consultaData.nombre_consulta ?? '';
        if (DOM.detallesConsultaDiagnosticoBreveSpan) DOM.detallesConsultaDiagnosticoBreveSpan.textContent = consultaData.diagnostico_breve ?? '';
        if (DOM.detallesConsultaSexoSpan) DOM.detallesConsultaSexoSpan.textContent = consultaData.sexo ?? '';
        if (DOM.detallesConsultaEspecialidadSpan) DOM.detallesConsultaEspecialidadSpan.textContent = consultaData.especialidad ?? '';
        if (DOM.detallesConsultaFechaConsultaSpan) DOM.detallesConsultaFechaConsultaSpan.textContent = consultaData.fecha_consulta ?? '';
        if (DOM.detallesConsultaDiagnosticoDetalladoSpan) DOM.detallesConsultaDiagnosticoDetalladoSpan.textContent = consultaData.diagnostico_detallado ?? '';
        console.log("ModalManager: Spans de detalles de consulta actualizados.");

        // Configurar el botón "Editar esta consulta"
        if (DOM.editThisConsultaButton) { // ID corregido
            DOM.editThisConsultaButton.onclick = () => ModalManager.openEditConsultaModal(consultaData);
            console.log("ModalManager: Botón 'Editar esta consulta' configurado.");
        }

        ModalManager.open(DOM.detallesConsultaModalOverlay);
        console.log("ModalManager: openDetallesConsultaModal finalizada.");
    },

    /**
     * Recopila los datos del paciente principal desde los elementos del DOM.
     * @returns {Object} Un objeto con los datos del paciente principal.
     */
    _getPacientePrincipalDataFromDOM: function() {
        // Tu HTML tiene los datos del paciente principal directamente en <p> tags sin IDs específicos para cada span.
        // Vamos a extraerlos de los textContent de los <p> y limpiarlos.
        const getTextContent = (selector, prefix) => {
            const element = document.querySelector(selector);
            return element ? element.textContent.replace(prefix, '').trim() : '';
        };

        // NOTA: Para que esto funcione, necesitas que los <p> tags en #detalles-paciente tengan IDs si quieres acceder a ellos directamente.
        // Si no tienen IDs, puedes obtener el texto del p y parsearlo. Lo ideal es que el PHP los ponga en spans con IDs.
        // Por ahora, lo haré usando selectores de p, pero es menos robusto.
        // Lo ideal sería que tu HTML para detalles-paciente fuera así (o similar):
        // <p><strong>ID:</strong> <span id="clienteId"></span></p>
        // <p><strong>Nombre del paciente:</strong> <span id="nombre-paciente"></span></p>
        // ... y así sucesivamente.

        // Por ahora, intentaremos extraerlo del textContent completo del <p>
        return {
            // El ID del paciente principal se encuentra en el PHP como $paciente_principal_data['id']
            // Debería ser pasado a un campo oculto o un atributo data en el botón de editar paciente
            // Por ejemplo: <button id="openEditPacienteModalBtn" data-cliente-id="<?php echo htmlspecialchars($paciente_principal_data['id'] ?? ''); ?>">
            // Para obtenerlo del DOM actual, lo sacamos del primer <p>
            id: getTextContent('#detalles-paciente p:nth-child(1)', 'ID:'), // Asume que el primer p es el ID
            nombre: getTextContent('#detalles-paciente p:nth-child(2)', 'Nombre del paciente:'),
            dni: getTextContent('#detalles-paciente p:nth-child(5)', 'DNI:'),
            direccion: getTextContent('#detalles-paciente p:nth-child(3)', 'Dirección:'),
            telefono: getTextContent('#detalles-paciente p:nth-child(4)', 'Teléfono:'),
            fechaNacimiento: getTextContent('#detalles-paciente p:nth-child(6)', 'Fecha de nacimiento:'),
            nacionalidad: getTextContent('#detalles-paciente p:nth-child(7)', 'Nacionalidad:'),
            sexo: getTextContent('#detalles-paciente p:nth-child(8)', 'Sexo:'),
            // No tienes un campo de "Fecha de Seguimiento" directamente visible en el HTML que proporcionaste para detalles-paciente.
            // Si existe, necesitarías añadir un selector para él.
            fechaSeguimientoInicio: getTextContent('#detalles-paciente p:nth-child(9)', 'Fecha de Seguimiento:') // Asumiendo que está ahí
        };
    },

    /**
     * Abre el modal para editar los detalles del paciente principal.
     * @param {Object} clienteData Datos del paciente principal a editar.
     */
    openEditPacientePrincipalModal: function(clienteData) {
        console.log("ModalManager: Abriendo modal para editar paciente principal. Datos:", clienteData);
        if(DOM.consultaModalOverlay) ModalManager.close(DOM.consultaModalOverlay);
        if(DOM.detallesConsultaModalOverlay) ModalManager.close(DOM.detallesConsultaModalOverlay);

        DOM.formEditarClientePrincipal?.reset();
        console.log("ModalManager: Formulario de paciente principal reseteado.");

        // Asignar valores a los campos del formulario
        // El input hidden para el ID del cliente principal ya lo tienes en el formulario de edición
        // <form id="formEditarPacientePrincipal"> ... <input type="hidden" name="editar_paciente_principal" value="1">
        // <input type="hidden" name="cliente_id_edit" value="ID_DEL_PACIENTE_AQUI">
        // Si no tienes un ID específico en el form, el PHP lo leerá de la URL.
        // Pero si tuvieras un input con ID="clienteIdEdit", sería: DOM.clienteIdEdit.value = clienteData.id ?? '';
        // Por ahora, los datos del paciente principal se pasarán a los inputs que tienen sus propios IDs.

        if (DOM.clienteNombreInput) DOM.clienteNombreInput.value = clienteData.nombre ?? '';
        if (DOM.clienteDniInput) DOM.clienteDniInput.value = clienteData.dni ?? '';
        if (DOM.clienteDireccionInput) DOM.clienteDireccionInput.value = clienteData.direccion ?? '';
        if (DOM.clienteTelefonoInput) DOM.clienteTelefonoInput.value = clienteData.telefono ?? '';
        if (DOM.clienteFechaNacimientoInput) DOM.clienteFechaNacimientoInput.value = clienteData.fechaNacimiento ?? '';
        if (DOM.clienteNacionalidadInput) DOM.clienteNacionalidadInput.value = clienteData.nacionalidad ?? '';
        if (DOM.clienteSexoInput && clienteData.sexo) DOM.clienteSexoInput.value = clienteData.sexo; // Para el select
        if (DOM.clienteFechaSeguimientoInicioInput) DOM.clienteFechaSeguimientoInicioInput.value = clienteData.fechaSeguimientoInicio ?? '';


        // Ya tienes un input hidden con name="editar_paciente_principal" value="1" en tu HTML
        // No necesitas otro campo para el action type si lo manejas así.
        // Si el JS necesitara cambiar ese valor:
        // const actionInput = DOM.formEditarClientePrincipal?.querySelector('input[name="editar_paciente_principal"]');
        // if (actionInput) actionInput.value = '1';

        if (DOM.submitEditClientePrincipalBtn) DOM.submitEditClientePrincipalBtn.textContent = 'Guardar Cambios del Paciente';

        ModalManager.open(DOM.editPacientePrincipalModalOverlay);
        console.log("ModalManager: openEditPacientePrincipalModal finalizada.");
    }
};

console.log("Funciones de control de modales definidas.");

// 3. Funciones Adicionales
console.log("Definiendo funciones adicionales.");

const Utils = {
    /**
     * Función para descargar la tarjeta del paciente como imagen.
     */
    descargarTarjeta: function() {
        console.log("Utils.descargarTarjeta: Iniciada.");
        const tarjetaDiv = DOM.tarjetaParaDescargar; // Usar la referencia del DOM centralizada

        if (!tarjetaDiv) {
            console.error("Utils.descargarTarjeta: No se encontró el elemento 'tarjeta-para-descargar'.");
            return;
        }
        if (typeof html2canvas === 'undefined') {
            console.error("html2canvas no está cargado. Asegúrate de incluir la librería en tu HTML antes de este script.");
            alert("Error: La librería html2canvas no está disponible para descargar la imagen.");
            return;
        }

        html2canvas(tarjetaDiv, {
            scale: 2 // Aumenta la resolución para una mejor calidad
        }).then(canvas => {
            const link = document.createElement('a');
            link.download = 'detalles_paciente.png';
            link.href = canvas.toDataURL('image/png');
            link.click();
            console.log("Utils.descargarTarjeta: Tarjeta generada y enlace de descarga activado.");
        }).catch(error => {
            console.error("Utils.descargarTarjeta: Error al generar la imagen de la tarjeta:", error);
            alert("Hubo un error al descargar la tarjeta como imagen.");
        });
        console.log("Utils.descargarTarjeta: Solicitud de generación de imagen iniciada.");
    },

    /**
     * Envía una solicitud para eliminar una consulta.
     * @param {string} consultaId El ID de la consulta a eliminar.
     */
    deleteConsulta: function(consultaId) { // Renombrado para consistencia
        console.log(`Utils.deleteConsulta: Iniciando solicitud de eliminación para ID: ${consultaId}.`);
        fetch('agregar-consulta.php', { // Ruta relativa, asumiendo que está en la misma carpeta o ruta accesible
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `consulta_id=${consultaId}&action=delete_consulta` // Asegúrate que el PHP espera 'consulta_id' y 'action=delete_consulta'
        })
        .then(response => {
            console.log("Utils.deleteConsulta: Respuesta recibida del servidor.");
            if (!response.ok) {
                console.error(`Utils.deleteConsulta: Error HTTP ${response.status} - ${response.statusText}`);
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            console.log("Utils.deleteConsulta: Datos de respuesta JSON del servidor:", data);
            if (data.success) {
                alert(data.message);
                console.log("Utils.deleteConsulta: Eliminación exitosa. Recargando página...");
                // Eliminar visualmente la tarjeta de la consulta sin recargar
                const deletedCard = document.querySelector(`.consulta-item .delete-consulta-btn[data-consulta-id="${consultaId}"]`)?.closest('.consulta-item');
                if (deletedCard) {
                    deletedCard.remove();
                    console.log(`Utils.deleteConsulta: Elemento de consulta ID ${consultaId} removido del DOM.`);
                }
                // Si prefieres recargar la página:
                // window.location.reload();
            } else {
                alert(`Error al eliminar la consulta: ${data.message}`);
                console.error(`Utils.deleteConsulta: Fallo en la eliminación reportado por el servidor: ${data.message}`);
            }
        })
        .catch(error => {
            console.error('Utils.deleteConsulta: Error al enviar la solicitud de eliminación o al procesar la respuesta:', error);
            alert('Ocurrió un error al intentar eliminar la consulta. Por favor, inténtalo de nuevo.');
        });
        console.log("Utils.deleteConsulta: Solicitud Fetch iniciada.");
    }
};

// Hacer la función de descargar tarjeta global (si ya está en el HTML)
window.descargarTarjeta = Utils.descargarTarjeta;

console.log("Funciones adicionales definidas.");

// 4. Configuración de Event Listeners
console.log("Configurando Event Listeners.");

function setupEventListeners() {
    // Pestañas
    DOM.tabButtons.forEach(button => {
        button.addEventListener('click', () => {
            console.log(`Event Listener: Clic en botón de pestaña '${button.dataset.tab}'.`);
            DOM.tabButtons.forEach(btn => btn.classList.remove('active'));
            DOM.tabContents.forEach(content => content.style.display = 'none');

            button.classList.add('active');
            const targetTab = button.dataset.tab;
            const targetContent = getElement(targetTab, `Contenido de pestaña ${targetTab}`);
            if (targetContent) {
                targetContent.style.display = 'block';
                console.log(`Event Listener: Pestaña '${targetTab}' activada.`);
            }

            // Si la pestaña "Agregar Consulta" se refiere a mostrar el modal, la lógica iría aquí.
            // Pero en tu HTML, la pestaña 'agregar-consulta-tab' es un div que contiene el formulario directamente.
            // Si el objetivo fuera que la pestaña "Agregar Consulta" abriera un modal, necesitarías:
            // if (targetTab === 'agregar-consulta-tab') {
            //     ModalManager.openAddConsultaModal();
            //     // Y quizás cerrar la pestaña o manejar su estado
            //     // Dejar el display en 'none' para 'agregar-consulta-tab' si el modal es flotante.
            // }
        });
    });

    // Botón de descarga de tarjeta
    DOM.descargarTarjetaBtn?.addEventListener('click', () => {
        console.log("Event Listener: Clic en 'descargarTarjetaBtn'.");
        Utils.descargarTarjeta();
    });

    // Botones para cerrar modales al hacer clic directamente en ellos
    DOM.closeConsultaModalBtn?.addEventListener('click', () => ModalManager.close(DOM.consultaModalOverlay)); // ID corregido
    DOM.closeDetallesConsultaModalBtn?.addEventListener('click', () => ModalManager.close(DOM.detallesConsultaModalOverlay));
    DOM.closeEditPacientePrincipalModalBtn?.addEventListener('click', () => ModalManager.close(DOM.editPacientePrincipalModalOverlay));

    // Cerrar modales al hacer clic fuera del contenido
    DOM.consultaModalOverlay?.addEventListener('click', (e) => { // ID corregido
        if (e.target === DOM.consultaModalOverlay) ModalManager.close(DOM.consultaModalOverlay);
    });
    DOM.detallesConsultaModalOverlay?.addEventListener('click', (e) => {
        if (e.target === DOM.detallesConsultaModalOverlay) ModalManager.close(DOM.detallesConsultaModalOverlay);
    });
    DOM.editPacientePrincipalModalOverlay?.addEventListener('click', (e) => {
        if (e.target === DOM.editPacientePrincipalModalOverlay) ModalManager.close(DOM.editPacientePrincipalModalOverlay);
    });

    // Delegación de eventos para botones de "Más información" y "Eliminar" (para elementos dinámicos)
    document.addEventListener('click', (event) => {
        // Clic en botón 'Más información' de consulta
        if (event.target.classList.contains('btn-info-consulta')) {
            console.log("Event Listener Delegado: Clic en botón 'Más información'.");
            const btn = event.target;
            const consultaData = {
                id: btn.dataset.id,
                nombre_consulta: btn.dataset.nombreConsulta,
                diagnostico_breve: btn.dataset.diagnosticoBreve,
                sexo: btn.dataset.sexo,
                especialidad: btn.dataset.especialidad,
                fecha_consulta: btn.dataset.fechaConsulta,
                diagnostico_detallado: btn.dataset.diagnosticoDetallado
            };
            console.log("Event Listener Delegado: Datos de consulta para 'Más información':", consultaData);
            ModalManager.openDetallesConsultaModal(consultaData);
        }

        // Clic en botón 'Eliminar consulta'
        if (event.target.classList.contains('delete-consulta-btn')) { // Clase corregida
            console.log("Event Listener Delegado: Clic en botón 'Eliminar consulta'.");
            const deleteButton = event.target;
            const consultaIdToDelete = deleteButton.dataset.consultaId; // data-consulta-id
            const nombreConsulta = deleteButton.dataset.nombreConsulta;

            console.log(`Event Listener Delegado: Preparando eliminación para consulta "${nombreConsulta}" (ID: ${consultaIdToDelete}).`);
            if (confirm(`¿Estás seguro de que quieres eliminar la consulta "${nombreConsulta}" (ID: ${consultaIdToDelete})? Esta acción es irreversible.`)) {
                console.log("Event Listener Delegado: Confirmación de eliminación ACEPTADA.");
                Utils.deleteConsulta(consultaIdToDelete); // Renombrado
            } else {
                console.log("Event Listener Delegado: Confirmación de eliminación CANCELADA.");
            }
        }
    });

    // Botón para abrir modal de edición de paciente principal
    DOM.openEditPacienteModalBtn?.addEventListener('click', () => {
        console.log("Event Listener: Clic en 'openEditPacienteModalBtn'.");
        const clienteData = ModalManager._getPacientePrincipalDataFromDOM(); // Obtener datos del DOM
        ModalManager.openEditPacientePrincipalModal(clienteData);
    });

    // Delegación para el botón "Editar esta consulta" dentro del modal de detalles
    // (Ya configurado dinámicamente en openDetallesConsultaModal)
}

console.log("Event Listeners configurados.");

// 5. Lógica de Inicialización (DOMContentLoaded)
console.log("Configurando Lógica de Inicialización (DOMContentLoaded).");

document.addEventListener('DOMContentLoaded', () => {
    console.log("DOMContentLoaded: El DOM ha sido completamente cargado y parseado.");

    // Lógica para Pestañas: Activar la primera pestaña al cargar o la especificada en el hash
    if (DOM.tabButtons.length > 0 && DOM.tabContents.length > 0) {
        const hash = window.location.hash.substring(1);
        let activated = false;
        if (hash) {
            const targetButton = document.querySelector(`.tab-button[data-tab="${hash}"]`);
            if (targetButton) {
                targetButton.click(); // Simula un clic para activar la pestaña
                activated = true;
                console.log(`DOMContentLoaded: Pestaña activada por hash URL: '${hash}'.`);
            }
        }
        if (!activated) {
            if (DOM.tabButtons[0]) {
                DOM.tabButtons[0].click(); // Activa la primera pestaña si no hay hash o no coincide
                console.log("DOMContentLoaded: Se activó la primera pestaña por defecto.");
            } else {
                console.warn("DOMContentLoaded: No hay botones de pestaña para activar por defecto.");
            }
        }
    }
    
    // Lógica para abrir modal de detalles de consulta automáticamente (si viene de PHP)
    // El PHP inline en detalle-propietario.php?id=9&success_mascota=... genera un script in-line.
    // Ese script in-line es el que debería llamar a ModalManager.openDetallesConsultaModal(mascotaData).
    // Tu script in-line actual usa 'consulta_id' o 'id' de la URL, que es una forma válida, pero debe pasarlos al JS.
    // El 'data-mascota-data' en el body no está en tu HTML, así que esa parte del JS no se activará.
    // Si tu PHP ya tiene un script in-line que maneja esto, asegúrate de que llama a la función correcta
    // con los datos necesarios (ej. window.onload = () => ModalManager.openDetallesConsultaModal({id: '...', nombre_consulta: '...'})).

    setupEventListeners(); // Llama a la configuración de eventos DESPUÉS de que el DOM esté cargado
});
console.log("Lógica de Inicialización configurada.");
console.log("Archivo detalle-propietario.js cargado y ejecutado.");
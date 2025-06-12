  
// Archivo: detalle-propietario.js
  
  // Variables de los modales
    const openAddMascotaModalBtn = document.querySelector('.open-add-mascota-modal-btn');

    // Modal de Agregar/Editar Consulta (el SEGUNDO modal)
    const mascotaModalOverlay = document.getElementById('mascotaModalOverlay');
    const closeMascotaModalBtn = document.getElementById('closeMascotaModalBtn');
    const mascotaModalTitle = document.getElementById('mascotaModalTitle');
    const mascotaFormActionType = document.getElementById('mascota_form_action_type');
    const mascotaIdEdit = document.getElementById('mascota_id_edit');
    const mascotaPropietarioId = document.getElementById('mascota_propietario_id');
    const mascotaNombreInput = document.getElementById('mascota_nombre_input');
    const mascotaNacionalidadInput = document.getElementById('mascota_nacionalidad_input');
    const mascotaDiagnosticoInput = document.getElementById('mascota_diagnostico_input');
    const mascotaSexoInput = document.getElementById('mascota_sexo_input');
    const mascotaEspecialidadInput = document.getElementById('mascota_especialidad_input');
    const mascotaFechaNacimientoInput = document.getElementById('mascota_fechaNacimiento_input');
    const submitMascotaModalBtn = document.getElementById('submitMascotaModalBtn');

    // Modal de Detalles de Consulta Específica (el PRIMER modal)
    const detallesConsultaModalOverlay = document.getElementById('detallesConsultaModalOverlay');
    const closeDetallesConsultaModalBtn = document.getElementById('closeDetallesConsultaModalBtn');
    const detallesConsultaIdSpan = document.getElementById('detallesConsultaId');
    const detallesConsultaNombreSpan = document.getElementById('detallesConsultaNombre');
    const detallesConsultaNacionalidadSpan = document.getElementById('detallesConsultaNacionalidad');
    const detallesConsultaDiagnosticoSpan = document.getElementById('detallesConsultaDiagnostico');
    const detallesConsultaSexoSpan = document.getElementById('detallesConsultaSexo');
    const detallesConsultaEspecialidadSpan = document.getElementById('detallesConsultaEspecialidad');
    const detallesConsultaFechaNacimientoSpan = document.getElementById('detallesConsultaFechaNacimiento');
    const editThisMascotaButton = document.getElementById('editThisMascotaButton');
    // Modal de Edición de Paciente Principal (el TERCER modal)
    const openEditPacienteModalBtn = document.getElementById('openEditPacienteModalBtn');
    const editPacientePrincipalModalOverlay = document.getElementById('editPacientePrincipalModalOverlay');
    const closeEditPacientePrincipalModalBtn = document.getElementById('closeEditPacientePrincipalModalBtn');


    // Función para abrir el modal para AGREGAR una nueva "mascota" (consulta)
    function openAddMascotaModal() {
        mascotaModalOverlay.classList.add('active'); // Usar .active para mostrar
        mascotaModalTitle.textContent = "Agregar Nueva Consulta para <?php echo htmlspecialchars($cliente_data['nombre']); ?>";
        mascotaFormActionType.name = 'agregar_mascota';
        mascotaFormActionType.value = '1';
        mascotaIdEdit.value = ''; // Limpiar ID de mascota para agregar
        mascotaNombreInput.value = ''; // Limpiar campos
        mascotaNacionalidadInput.value = '';
        mascotaDiagnosticoInput.value = '';
        mascotaSexoInput.value = 'masculino'; // Default value
        mascotaEspecialidadInput.value = '';
        mascotaFechaNacimientoInput.value = '';
        submitMascotaModalBtn.textContent = 'Guardar Nueva Consulta';
        mascotaPropietarioId.value = "<?php echo htmlspecialchars($cliente_id); ?>";
    }
    // Función para abrir el modal para EDITAR una "mascota" (consulta) existente
    function openEditMascotaModal(mascotaData) {
        // Cierra el modal de detalles de consulta si está abierto
        detallesConsultaModalOverlay.classList.remove('active');
        mascotaModalOverlay.classList.add('active'); // Abre el modal de edición
        mascotaModalTitle.textContent = `Editar Consulta (pasiente selecionado ID: ${mascotaData.id})`;
        mascotaFormActionType.name = 'editar_mascota';
        mascotaFormActionType.value = '1';
        mascotaIdEdit.value = mascotaData.id; // Cargar el ID de la mascota a editar
        mascotaNombreInput.value = mascotaData.nombre;
        mascotaNacionalidadInput.value = mascotaData.nacionalidad;
        mascotaDiagnosticoInput.value = mascotaData.diagnostico;
        mascotaSexoInput.value = mascotaData.sexo;
        mascotaEspecialidadInput.value = mascotaData.especialidad;
        mascotaFechaNacimientoInput.value = mascotaData.fechaNacimiento;
        submitMascotaModalBtn.textContent = 'Guardar Cambios de Consulta';
        mascotaPropietarioId.value = "<?php echo htmlspecialchars($cliente_id); ?>";
    }
    // Función para abrir el modal de Detalles de Consulta Específica
    function openDetallesConsultaModal(mascotaData) {
        detallesConsultaIdSpan.textContent = mascotaData.id;
        detallesConsultaNombreSpan.textContent = mascotaData.nombre;
        detallesConsultaNacionalidadSpan.textContent = mascotaData.nacionalidad;
        detallesConsultaDiagnosticoSpan.textContent = mascotaData.diagnostico;
        detallesConsultaSexoSpan.textContent = mascotaData.sexo;
        detallesConsultaEspecialidadSpan.textContent = mascotaData.especialidad;
        detallesConsultaFechaNacimientoSpan.textContent = mascotaData.fechaNacimiento;
        // Configurar el botón "Editar esta consulta" para abrir el modal de edición
        editThisMascotaButton.onclick = () => openEditMascotaModal(mascotaData);ñ
        detallesConsultaModalOverlay.classList.add('active');
    }
    // Event listener para el botón "Agregar Nueva Consulta"
    if (openAddMascotaModalBtn) {
        openAddMascotaModalBtn.addEventListener('click', () => openAddMascotaModal());
    }
    // Cerrar el modal de agregar/editar mascota
    if (closeMascotaModalBtn) {
        closeMascotaModalBtn.addEventListener('click', () => {
            mascotaModalOverlay.classList.remove('active');
            // Limpiar el parámetro mascota_id de la URL al cerrar el modal de edición
            const url = new URL(window.location.href);
            url.searchParams.delete('mascota_id');
            window.history.replaceState({}, document.title, url.toString());
        });
    }

    // Cerrar el modal de detalles de consulta
    if (closeDetallesConsultaModalBtn) {
        closeDetallesConsultaModalBtn.addEventListener('click', () => {
            detallesConsultaModalOverlay.classList.remove('active');
            // Limpiar el parámetro mascota_id de la URL al cerrar el modal de detalles
            const url = new URL(window.location.href);
            url.searchParams.delete('mascota_id');
            window.history.replaceState({}, document.title, url.toString());
        });
    }
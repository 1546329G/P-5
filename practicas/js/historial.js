document.addEventListener("DOMContentLoaded", () => {
    const addForm = document.getElementById("addForm");
    const pacienteIdInput = document.getElementById("paciente_id"); // Cambiado de cliente_id
    const consultaIdInput = document.getElementById("consulta_id"); // Cambiado de mascota_id
    const fechaVisitaInput = document.getElementById("fecha_visita");
    const descripcionInput = document.getElementById("descripcion");

    const modalOverlay = document.getElementById("modalOverlay");
    const closeModalBtn = document.getElementById("closeModalBtn");

    function abrirModal(pacienteId, consultaId) { // Cambiados parámetros
        pacienteIdInput.value = pacienteId;
        consultaIdInput.value = consultaId;
        fechaVisitaInput.value = "";
        descripcionInput.value = "";
        modalOverlay.style.display = "block";
    }

    closeModalBtn.addEventListener("click", () => {
        modalOverlay.style.display = "none";
    });

    addForm.addEventListener("submit", async (event) => {
        event.preventDefault();

        const formData = new FormData(addForm);
        try {
            const response = await fetch("php/agregar-historial.php", {
                method: "POST",
                body: formData,
            });

            const result = await response.json();

            if (result.success) {
                alert(result.success);
                modalOverlay.style.display = "none";
                cargarHistorial(pacienteIdInput.value); // Cambiado a pacienteIdInput
            } else {
                alert(result.error);
            }
        } catch (error) {
            console.error("Error:", error);
            alert("Ocurrió un error al guardar la visita."); // Mensaje actualizado
        }
    });

    document.querySelectorAll(".btn-agregar-descripcion").forEach((button) => {
        button.addEventListener("click", (event) => {
            const pacienteId = button.dataset.pacienteId; // Cambiado a pacienteId
            const consultaId = button.dataset.consultaId; // Cambiado a consultaId
            abrirModal(pacienteId, consultaId); // Cambiados argumentos
        });
    });

    async function cargarHistorial(pacienteId) { // Cambiado parámetro
        try {
            const response = await fetch(`php/obtener_historial.php?paciente_id=${pacienteId}`); // Cambiado a paciente_id
            const data = await response.json();

            const historialContainer = document.getElementById("lista-historial");
            historialContainer.innerHTML = "";

            data.forEach((item) => {
                const li = document.createElement("li");
                li.textContent = `${item.fecha_visita}: ${item.descripcion}`;
                historialContainer.appendChild(li);
            });
        } catch (error) {
            console.error("Error al cargar historial:", error);
        }
    }
});
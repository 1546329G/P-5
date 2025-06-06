document.addEventListener("DOMContentLoaded", () => {
    const addForm = document.getElementById("addForm");
    const clienteIdInput = document.getElementById("cliente_id");
    const mascotaIdInput = document.getElementById("mascota_id");
    const fechaVisitaInput = document.getElementById("fecha_visita");
    const descripcionInput = document.getElementById("descripcion");

    const modalOverlay = document.getElementById("modalOverlay");
    const closeModalBtn = document.getElementById("closeModalBtn");

    // Abre el modal para agregar descripción
    function abrirModal(clienteId, mascotaId) {
        clienteIdInput.value = clienteId;
        mascotaIdInput.value = mascotaId;
        fechaVisitaInput.value = ""; // Limpiar campos
        descripcionInput.value = "";
        modalOverlay.style.display = "block";
    }

    // Cierra el modal
    closeModalBtn.addEventListener("click", () => {
        modalOverlay.style.display = "none";
    });

    // Enviar datos del formulario
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

                // Recargar el historial de visitas
                cargarHistorial(clienteIdInput.value);
            } else {
                alert(result.error);
            }
        } catch (error) {
            console.error("Error:", error);
            alert("Ocurrió un error al guardar la descripción.");
        }
    });

    // Simulación de cómo abrir el modal (reemplaza esto con tu lógica)
    document.querySelectorAll(".btn-agregar-descripcion").forEach((button) => {
        button.addEventListener("click", (event) => {
            const clienteId = button.dataset.clienteId; // Agrega estos atributos a los botones
            const mascotaId = button.dataset.mascotaId;
            abrirModal(clienteId, mascotaId);
        });
    });

    // Función para cargar historial
    async function cargarHistorial(clienteId) {
        try {
            const response = await fetch(`php/obtener_historial.php?cliente_id=${clienteId}`);
            const data = await response.json();

            const historialContainer = document.getElementById("lista-historial");
            historialContainer.innerHTML = ""; // Limpia el historial anterior

            data.forEach((item) => {
                const li = document.createElement("li");
                li.textContent = `${item.fecha_visita}: ${item.descripcion}`;
                historialContainer.appendChild(li);
            });
        } catch (error) {
            console.error("Error al cargar historial:", error);
        }
    }

    if ($_SERVER["REQUEST_METHOD"] !== "POST") {
        echo json_encode(['error' => 'Método no permitido.', 'metodo_recibido' => $_SERVER["REQUEST_METHOD"]]);
        exit;
    }
    
});

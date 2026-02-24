document.addEventListener('DOMContentLoaded', function() {
    const formEscuela = document.querySelector('form');

    formEscuela.addEventListener('submit', async (e) => {
        e.preventDefault(); // Evita que la página se recargue

        // Creamos el FormData para manejar texto y archivos (logo)
        const formData = new FormData(formEscuela);
        
        try {
            const response = await fetch( BASE_URL +'api/escuelas.php?tipo=agregar_escuela', {
                method: 'POST',
                body: formData
            });

            const result = await response.json();

            if (result.estatus === true) {
               Swal.fire({icon: 'success', title: result.mensaje, confirmButtonText: 'Entendido'})
                formEscuela.reset(); // Limpia el formulario
            } else {
                Swal.fire({icon: 'error', title: result.mensaje, confirmButtonText: 'Entendido'})
            }
        } catch (error) {
            Swal.fire({icon: 'error', title: 'Error al comunicarse con el servidor'})

        }
    });
});
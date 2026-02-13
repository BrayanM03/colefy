export async function getRoleFromServer() {
    try {
        // Asume que este endpoint devuelve la información del usuario, incluyendo el rol
        const response = await fetch('/api/user/usuarios', {
            headers: {
                'Authorization': `Bearer ${localStorage.getItem('token')}` 
            }
        });
        const data = await response.json();
        
        return data.role; // Devuelve solo el rol

    } catch (error) {
        console.error("Error al obtener el rol del servidor:", error);
        return null;
    }
}
// Puedes añadir otras funciones de uso común aquí
// export function formatCurrency(value) { ... }
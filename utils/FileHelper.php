<?php
declare(strict_types=1);

class FileHelper {
    /**
     * Procesa la subida de una imagen y elimina la anterior si existe.
     * * @param array $file El array de $_FILES['input_name']
     * @param string $folder Carpeta destino dentro de static/img/
     * @param string $prefix Prefijo para el nombre (perfil, escuela, etc)
     * @param string|null $oldFile Nombre del archivo anterior para borrar
     * @return string|null Nombre del nuevo archivo o null si falla
     */
    public static function uploadImage(array $file, string $folder, string $prefix, ?string $oldFile = null): ?string {
        $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $nuevo_nombre = $prefix . "_" . time() . "." . $extension;
        
        $uploadDir = ROOT_PATH . "static/img/" . $folder . "/";
        $ruta_destino = $uploadDir . $nuevo_nombre;
    
        // Crear carpeta si no existe
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        if (move_uploaded_file($file['tmp_name'], $ruta_destino)) {
            // Borrar archivo anterior si existe y no es el default
            if (!empty($oldFile) && $oldFile !== 'avatar.jpg' && $oldFile !== 'default.png') {
                $ruta_vieja = $uploadDir . $oldFile;
                if (file_exists($ruta_vieja)) {
                    unlink($ruta_vieja);
                } 
            }
            return $nuevo_nombre;
        }

        return null;
    }
}
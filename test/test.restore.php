<?php
/**
 * Archivo: test.restore.php
 * Autor: YAGUAR, Eduardo
 * Versión: 1.1
 * Descripción: Prueba la restauración de la última copia de seguridad cifrada disponible con salida optimizada para navegador y CLI.
 */

require_once __DIR__ . '/../backup/restore.php';

// Detectar si se ejecuta en navegador o CLI
$is_cli = php_sapi_name() === 'cli';

// Función para mostrar mensajes con formato según el entorno
function showMessage($message, $type = 'info') {
    global $is_cli;

    if ($is_cli) {
        echo $message . PHP_EOL;
    } else {
        $color = match ($type) {
            'success' => 'green',
            'error' => 'red',
            'info' => 'blue',
            'warning' => 'orange',
            default => 'black'
        };
        echo "<div style='color: white; background-color: {$color}; padding: 10px; margin: 5px; border-radius: 5px;'>{$message}</div>";
    }
}

// Ruta del directorio donde se almacenan los backups cifrados
$backup_dir = __DIR__ . '/../backup/';
$backup_files = glob($backup_dir . 'backup_*.enc');

// Verificar si hay archivos de respaldo disponibles
if (!$backup_files) {
    showMessage("❌ No se encontraron archivos de respaldo cifrados en {$backup_dir}", "error");
    exit;
}

// Ordenar los archivos por fecha (el más reciente primero)
usort($backup_files, function ($a, $b) {
    return filemtime($b) - filemtime($a);
});

// Tomar el archivo más reciente
$latest_backup = $backup_files[0];

showMessage("🔍 Iniciando prueba de restauración...", "info");
showMessage("📂 Archivo de respaldo encontrado: {$latest_backup}", "success");

// Crear una instancia de la clase DatabaseRestore
$restore = new DatabaseRestore();

// Restaurar la copia de seguridad cifrada
$restore->restoreEncryptedBackup($latest_backup);

showMessage("✅ Prueba de restauración finalizada.", "success");
?>

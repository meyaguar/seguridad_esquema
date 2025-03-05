<?php
/**
 * Archivo: test.backup.php
 * Autor: YAGUAR, Eduardo
 * Versión: 1.1
 * Descripción: Prueba la generación de una copia de seguridad cifrada con salida mejorada para navegador y CLI.
 */

require_once __DIR__ . '/../backup/backup.php';

// Detectar si se ejecuta en navegador o CLI
$is_cli = php_sapi_name() === 'cli';

// Función para mostrar mensajes con formato
function showMessage($message, $type = 'info') {
    global $is_cli;

    if ($is_cli) {
        echo $message . PHP_EOL;
    } else {
        $color = match ($type) {
            'success' => 'green',
            'error' => 'red',
            'info' => 'blue',
            default => 'black'
        };
        echo "<div style='color: white; background-color: {$color}; padding: 10px; margin: 5px; border-radius: 5px;'>{$message}</div>";
    }
}

// Mensaje de inicio
showMessage("🔍 Iniciando prueba de respaldo...", "info");

// Crear una instancia de la clase DatabaseBackup
$backup = new DatabaseBackup();

// Generar una copia de seguridad cifrada
$backup_file = $backup->createEncryptedBackup();

if ($backup_file) {
    showMessage("✅ Prueba exitosa. Copia de seguridad cifrada creada: {$backup_file}", "success");
} else {
    showMessage("❌ Error en la generación del respaldo.", "error");
}
?>

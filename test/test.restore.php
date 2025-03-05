<?php
/**
 * Archivo: test.restore.php
 * Autor: YAGUAR, Eduardo
 * Versión: 1.0
 * Descripción: Prueba la restauración de la última copia de seguridad cifrada disponible.
 */

require_once __DIR__ . '/../backup/restore.php';

// Ruta del directorio donde se almacenan los backups cifrados
$backup_dir = __DIR__ . '/../backup/';
$backup_files = glob($backup_dir . 'backup_*.enc');

// Verificar si hay archivos de respaldo disponibles
if (!$backup_files) {
    die("❌ No se encontraron archivos de respaldo cifrados en {$backup_dir}\n");
}

// Ordenar los archivos por fecha (el más reciente primero)
usort($backup_files, function ($a, $b) {
    return filemtime($b) - filemtime($a);
});

// Tomar el archivo más reciente
$latest_backup = $backup_files[0];

echo "🔍 Iniciando prueba de restauración...\n";
echo "📂 Archivo de respaldo encontrado: {$latest_backup}\n";

// Crear una instancia de la clase DatabaseRestore
$restore = new DatabaseRestore();

// Restaurar la copia de seguridad cifrada
$restore->restoreEncryptedBackup($latest_backup);

echo "✅ Prueba de restauración finalizada.\n";
?>

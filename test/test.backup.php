<?php
/**
 * Archivo: test.backup.php
 * Autor: YAGUAR, Eduardo
 * Versión: 1.0
 * Descripción: Prueba la generación de una copia de seguridad cifrada.
 */

require_once __DIR__ . '/../backup/backup.php';

echo "🔍 Iniciando prueba de respaldo...\n";

// Crear una instancia de la clase DatabaseBackup
$backup = new DatabaseBackup();

// Generar una copia de seguridad cifrada
$backup_file = $backup->createEncryptedBackup();

if ($backup_file) {
    echo "✅ Prueba exitosa. Copia de seguridad cifrada creada: {$backup_file}" . PHP_EOL;
} else {
    echo "❌ Error en la generación del respaldo." . PHP_EOL;
}
?>

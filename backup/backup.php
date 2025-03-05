<?php
/**
 * Archivo: backup.php
 * Autor: YAGUAR, Eduardo
 * Versión: 1.2
 * Descripción: Genera y cifra copias de seguridad de la base de datos en formato SQL.
 */

require_once __DIR__ . '/../encryption/aes_encryption.php';

// Función para cargar variables desde .env manualmente si no están definidas
function loadEnv($envPath) {
    if (!file_exists($envPath)) {
        die("Error: Archivo .env no encontrado." . PHP_EOL);
    }
    $lines = file($envPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) continue; // Ignorar comentarios
        putenv($line);
    }
}

// Cargar variables de entorno desde .env
$envFile = __DIR__ . '/../.env';
loadEnv($envFile);

class DatabaseBackup {
    private $host;
    private $db_name;
    private $username;
    private $password;
    private $backup_dir;
    private $aes;

    /**
     * Constructor que carga las credenciales de la base de datos y el cifrado AES.
     */
    public function __construct() {
        $this->host = getenv('DB_HOST') ?: '127.0.0.1';
        $this->db_name = getenv('DB_DATABASE') ?: 'database';
        $this->username = getenv('DB_USERNAME') ?: 'root';
        $this->password = getenv('DB_PASSWORD') ?: '';
        $this->backup_dir = __DIR__ . '/../backup/';
        $encryption_key = getenv('BACKUP_ENCRYPTION_KEY') ?: 'ClaveSeguraParaBackup2025';
        $this->aes = new AESEncryption($encryption_key);
    }

    /**
     * Genera un respaldo de la base de datos y lo cifra con AES-256-CBC.
     * @return string Nombre del archivo de respaldo cifrado generado.
     */
    public function createEncryptedBackup() {
        // Verifica si el directorio de respaldo existe, si no, lo crea.
        if (!file_exists($this->backup_dir)) {
            mkdir($this->backup_dir, 0777, true);
        }

        $timestamp = date('Ymd_His');
        $backup_file = "backup_{$this->db_name}_{$timestamp}.sql";
        $backup_path = "{$this->backup_dir}{$backup_file}";
        $encrypted_backup_path = "{$this->backup_dir}backup_{$this->db_name}_{$timestamp}.enc";

        // Comando para generar el respaldo en formato SQL
        $command = "mysqldump --host={$this->host} --user={$this->username} --password=\"{$this->password}\" {$this->db_name} > {$backup_path}";
        system($command, $output);

        // Verifica si el respaldo se generó correctamente
        if (file_exists($backup_path)) {
            // Leer el contenido del respaldo
            $backup_content = file_get_contents($backup_path);
            
            // Cifrar el respaldo
            $encrypted_backup = $this->aes->encrypt($backup_content);
            
            // Guardar el respaldo cifrado
            file_put_contents($encrypted_backup_path, $encrypted_backup);
            
            // Eliminar el respaldo original no cifrado
            unlink($backup_path);

            echo "Copia de seguridad cifrada creada: {$encrypted_backup_path}" . PHP_EOL;
            return $encrypted_backup_path;
        } else {
            echo "Error al generar la copia de seguridad." . PHP_EOL;
            return false;
        }
    }
}

// Uso de la clase para generar una copia de seguridad cifrada
$backup = new DatabaseBackup();
$backup_file = $backup->createEncryptedBackup();
?>

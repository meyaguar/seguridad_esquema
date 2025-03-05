<?php
/**
 * Archivo: restore.php
 * Autor: YAGUAR, Eduardo
 * Versión: 1.1
 * Descripción: Descifra y restaura una copia de seguridad cifrada en la base de datos.
 */

require_once __DIR__ . '/../encryption/aes_encryption.php';

// Función para cargar variables desde .env manualmente
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

class DatabaseRestore {
    private $host;
    private $db_name;
    private $username;
    private $password;
    private $aes;

    /**
     * Constructor que carga credenciales de la BD y el cifrado AES.
     */
    public function __construct() {
        $this->host = getenv('DB_HOST') ?: '127.0.0.1';
        $this->db_name = getenv('DB_DATABASE') ?: 'database';
        $this->username = getenv('DB_USERNAME') ?: 'root';
        $this->password = getenv('DB_PASSWORD') ?: '';
        $encryption_key = getenv('BACKUP_ENCRYPTION_KEY') ?: 'ClaveSeguraParaBackup2025';
        $this->aes = new AESEncryption($encryption_key);
    }

    /**
     * Descifra y restaura un respaldo cifrado en la base de datos.
     * @param string $backup_file Ruta del archivo cifrado.
     */
    public function restoreEncryptedBackup($backup_file) {
        if (!file_exists($backup_file)) {
            die("Error: El archivo de respaldo no existe." . PHP_EOL);
        }

        echo "Descifrando respaldo cifrado: {$backup_file}..." . PHP_EOL;

        // Leer el archivo cifrado
        $encrypted_content = file_get_contents($backup_file);
        
        // Descifrar el contenido
        $decrypted_sql = $this->aes->decrypt($encrypted_content);

        // Verificar que el descifrado fue exitoso
        if (!$decrypted_sql) {
            die("Error: No se pudo descifrar el respaldo." . PHP_EOL);
        }

        // Guardar el archivo SQL temporalmente
        $temp_sql_path = __DIR__ . "/temp_restore.sql";
        file_put_contents($temp_sql_path, $decrypted_sql);

        echo "Respaldo descifrado correctamente. Iniciando restauración en la base de datos..." . PHP_EOL;

        // Restaurar en MySQL
        $command = "mysql --host={$this->host} --user={$this->username} --password=\"{$this->password}\" {$this->db_name} < {$temp_sql_path}";
        system($command, $output);

        // Eliminar el archivo temporal después de restaurar
        unlink($temp_sql_path);

        echo "Restauración completada con éxito." . PHP_EOL;
    }
}

// Verifica si se ejecuta en CLI con un argumento
if (php_sapi_name() === 'cli') {
    if ($argc > 1) {
        $restore = new DatabaseRestore();
        $restore->restoreEncryptedBackup($argv[1]);
    } else {
        echo "Uso incorrecto. Debes proporcionar la ruta del archivo cifrado." . PHP_EOL;
        echo "   Ejemplo: php restore_encrypted.php /ruta/al/backup.enc" . PHP_EOL;
    }
}
?>

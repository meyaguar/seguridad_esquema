<?php
/**
 * Archivo: database.php
 * Autor: YAGUAR, Eduardo
 * Versión: 1.0
 * Descripción: Configuración de conexión a la base de datos utilizando PDO y variables de entorno.
 */

class Database {
    private $host;
    private $db_name;
    private $username;
    private $password;
    private $charset;
    private $pdo;

    /**
     * Constructor que carga la configuración desde las variables de entorno.
     */
    public function __construct() {
        $this->host = getenv('DB_HOST');
        $this->db_name = getenv('DB_DATABASE');
        $this->username = getenv('DB_USERNAME');
        $this->password = getenv('DB_PASSWORD');
        $this->charset = getenv('DB_CHARSET');
    }

    /**
     * Conecta a la base de datos y devuelve la instancia de PDO.
     * @return PDO|null Retorna una instancia de PDO o null si la conexión falla.
     */
    public function connect() {
        $this->pdo = null;
        try {
            $dsn = "mysql:host={$this->host};dbname={$this->db_name};charset={$this->charset}";
            $this->pdo = new PDO($dsn, $this->username, $this->password, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
            ]);
        } catch (PDOException $e) {
            die("Error de conexión: " . $e->getMessage());
        }
        return $this->pdo;
    }
}
?>

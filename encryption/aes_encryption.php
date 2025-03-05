<?php
/**
 * Archivo: aes_encryption.php
 * Autor: YAGUAR, Eduardo
 * Versión: 1.0
 * Descripción: Implementación de cifrado y descifrado simétrico utilizando AES-256-CBC.
 */

class AESEncryption {
    private $key;
    private $cipher = 'AES-256-CBC';

    /**
     * Constructor para inicializar la clave de cifrado.
     * @param string $key Clave secreta para el cifrado (debe ser de 32 bytes).
     */
    public function __construct($key) {
        $this->key = hash('sha256', $key, true); // Se genera una clave de 256 bits
    }

    /**
     * Cifra un texto utilizando AES-256-CBC.
     * @param string $data Texto a cifrar.
     * @return string Texto cifrado en base64.
     */
    public function encrypt($data) {
        $iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length($this->cipher)); // Genera un IV aleatorio
        $encrypted = openssl_encrypt($data, $this->cipher, $this->key, 0, $iv);
        return base64_encode($iv . $encrypted); // Se concatena IV y texto cifrado y se codifica en base64
    }

    /**
     * Descifra un texto cifrado con AES-256-CBC.
     * @param string $data Texto cifrado en base64.
     * @return string Texto original descifrado.
     */
    public function decrypt($data) {
        $data = base64_decode($data); // Decodifica el texto base64
        $iv_length = openssl_cipher_iv_length($this->cipher);
        $iv = substr($data, 0, $iv_length); // Extrae el IV
        $encrypted = substr($data, $iv_length); // Extrae el texto cifrado
        return openssl_decrypt($encrypted, $this->cipher, $this->key, 0, $iv);
    }
}
?>

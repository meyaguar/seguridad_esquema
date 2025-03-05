<?php
/**
 * Archivo: sha256_hash.php
 * Autor: YAGUAR, Eduardo
 * Versión: 1.0
 * Descripción: Implementación de generación y verificación de hash utilizando SHA-256.
 */

class SHA256Hash {
    /**
     * Genera un hash SHA-256 de un texto.
     * @param string $data Texto a hashear.
     * @return string Hash en formato hexadecimal.
     */
    public static function generateHash($data) {
        return hash('sha256', $data);
    }

    /**
     * Verifica si un hash coincide con un texto dado.
     * @param string $data Texto original.
     * @param string $hash Hash esperado.
     * @return bool Verdadero si coincide, falso en caso contrario.
     */
    public static function verifyHash($data, $hash) {
        return hash_equals(self::generateHash($data), $hash);
    }
}
?>

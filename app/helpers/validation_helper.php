<?php

class ValidationHelper {
    /**
     * Valida se um campo está vazio
     * @param mixed $value
     * @return bool
     */
    public static function isEmpty($value) {
        return empty(trim($value));
    }

    /**
     * Valida se um email é válido
     * @param string $email
     * @return bool
     */
    public static function isValidEmail($email) {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }

    /**
     * Valida se um número é positivo
     * @param numeric $number
     * @return bool
     */
    public static function isPositiveNumber($number) {
        return is_numeric($number) && $number >= 0;
    }

    /**
     * Sanitiza uma string para evitar XSS
     * @param string $string
     * @return string
     */
    public static function sanitizeString($string) {
        return htmlspecialchars(strip_tags(trim($string)));
    }

    /**
     * Valida se um arquivo é uma imagem válida
     * @param array $file Array $_FILES do PHP
     * @return bool
     */
    public static function isValidImage($file) {
        if (!isset($file['type'])) {
            return false;
        }

        $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
        return in_array($file['type'], $allowed_types);
    }

    /**
     * Valida o tamanho máximo de um arquivo
     * @param array $file Array $_FILES do PHP
     * @param int $max_size Tamanho máximo em bytes
     * @return bool
     */
    public static function isValidFileSize($file, $max_size = 5242880) { // 5MB default
        return isset($file['size']) && $file['size'] <= $max_size;
    }
}
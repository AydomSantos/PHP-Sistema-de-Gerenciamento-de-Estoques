<?php

class AuthHelper {
    /**
     * Verifica se o usuário está autenticado
     * @return bool
     */
    public static function isAuthenticated() {
        return isset($_SESSION['user_id']);
    }

    /**
     * Verifica se o usuário tem permissão para acessar determinada rota
     * @param string $route Nome da rota
     * @return bool
     */
    public static function hasPermission($route) {
        if (!self::isAuthenticated()) {
            return false;
        }

        // Implementar lógica de permissões aqui
        // Por enquanto, retorna true para usuários autenticados
        return true;
    }

    /**
     * Redireciona para a página de login se não estiver autenticado
     */
    public static function requireAuth() {
        if (!self::isAuthenticated()) {
            header('Location: login.php');
            exit();
        }
    }

    /**
     * Faz logout do usuário
     */
    public static function logout() {
        session_destroy();
        header('Location: login.php');
        exit();
    }
}
<?php
/**
 * Funções utilitárias globais do sistema
 */

if (!function_exists('base_path')) {
    /**
     * Retorna o caminho base para assets (respeita execução via /public/)
     * @return string
     */
    function base_path(): string {
        $script = $_SERVER['SCRIPT_NAME'] ?? '';
        // Quando executado a partir de /public/index.php o caminho base é vazio
        return (strpos($script, '/public/') !== false) ? '' : '/Honra-e-Sombra/public/';
    }
}

if (!function_exists('asset')) {
    /**
     * Monta URL de asset com base_path
     * @param string $path Caminho relativo dentro de /public
     * @return string
     */
    function asset(string $path): string {
        return base_path() . ltrim($path, '/');
    }
}

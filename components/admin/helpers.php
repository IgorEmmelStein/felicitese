<?php
/**
 * Helpers do Painel Administrativo
 */

if (!function_exists('getInitials')) {
    function getInitials($fullName) {
        $parts = explode(' ', trim($fullName));
        $initials = '';
        if (!empty($parts[0])) {
            $initials .= mb_strtoupper(mb_substr($parts[0], 0, 1));
        }
        if (count($parts) > 1 && !empty($parts[count($parts) - 1])) {
            $initials .= mb_strtoupper(mb_substr($parts[count($parts) - 1], 0, 1));
        }
        return $initials ?: 'US';
    }
}

if (!function_exists('formatPostDate')) {
    function formatPostDate($dateString) {
        if (!$dateString) return 'Data não informada';
        $time = strtotime($dateString);
        $meses = ['Jan', 'Fev', 'Mar', 'Abr', 'Mai', 'Jun', 'Jul', 'Ago', 'Set', 'Out', 'Nov', 'Dez'];
        $dia = date('d', $time);
        $mes = $meses[(int)date('m', $time) - 1] ?? 'Mês';
        $ano = date('Y', $time);
        return "$dia $mes $ano";
    }
}

if (!function_exists('getCategoryBadgeClass')) {
    function getCategoryBadgeClass($categoryName) {
        $name = mb_strtolower(trim($categoryName));
        if (strpos($name, 'artigo') !== false) return 'artigos';
        if (strpos($name, 'evento') !== false || strpos($name, 'oficina') !== false) return 'eventos';
        if (strpos($name, 'notícia') !== false || strpos($name, 'noticia') !== false) return 'noticias';
        if (strpos($name, 'psico') !== false || strpos($name, 'saúde') !== false || strpos($name, 'saude') !== false) return 'psico';
        if (strpos($name, 'vivência') !== false || strpos($name, 'vivencia') !== false || strpos($name, 'lúdica') !== false || strpos($name, 'ludica') !== false) return 'vivencias';
        return 'artigos';
    }
}
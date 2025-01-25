<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Verificar si la función check_auth ya está definida
if (!function_exists('check_auth')) {
    // Verificar si el usuario está autenticado
    function check_auth() {
        if (!isset($_SESSION['usuario_id'])) {
            header("Location: login.php");
            exit();
        }
    }
}

// Verificar si la función check_admin ya está definida
if (!function_exists('check_admin')) {
    // Verificar si el usuario es administrador
    function check_admin() {
        if (!isset($_SESSION['usuario_id']) || $_SESSION['rol'] != 'administrador') {
            header("Location: login.php");
            exit();
        }
    }
}
?>
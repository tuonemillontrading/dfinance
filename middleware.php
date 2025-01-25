<?php
session_start();

function check_auth() {
    if (!isset($_SESSION['usuario_id'])) {
        header("Location: login.php");
        exit();
    }
}

function check_admin() {
    if (!isset($_SESSION['usuario_id']) || $_SESSION['rol'] != 'administrador') {
        header("Location: login.php");
        exit();
    }
}
?>
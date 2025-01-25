<?php
session_start();
require 'config.php';
require 'funcions.php';

// Verificar si el usuario está autenticado
if (!isset($_SESSION['usuario_id'])) {
    echo "Sesión no iniciada. Redirigiendo a login...";
    header('Location: login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'];
    $action = $_POST['action'];
    $password = $_POST['password'];

    // Verificar la contraseña administrativa
    if ($password === '1BillonUSD') { // Cambia '1BillonUSD' a tu contraseña real
        if ($action === 'aprobar') {
            $resultado = aprobarDeposito($pdo, $id);
            if ($resultado && $resultado['status']) {
                echo "<script>alert('Depósito aprobado'); window.location.href='aprobar_deposito.php';</script>";
                exit;
            } else {
                echo "<script>alert('Error al aprobar el depósito: " . htmlspecialchars($resultado['error']) . "'); window.location.href='aprobar_deposito.php';</script>";
                exit;
            }
        } elseif ($action === 'rechazar') {
            if (rechazarDeposito($pdo, $id)) {
                echo "<script>alert('Depósito rechazado'); window.location.href='aprobar_deposito.php';</script>";
                exit;
            } else {
                echo "<script>alert('Error al rechazar el depósito'); window.location.href='aprobar_deposito.php';</script>";
                exit;
            }
        }
    } else {
        echo "Contraseña incorrecta.";
    }
}
?>
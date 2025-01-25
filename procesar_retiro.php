<?php
include 'config.php'; // Incluir el archivo de configuración

// Verificar si una sesión ya está activa
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Verificar si 'usuario_id' está configurado en la sesión
if (!isset($_SESSION['usuario_id'])) {
    die("Error: No has iniciado sesión.");
}

$userId = $_SESSION['usuario_id'];
$cantidad = $_POST['cantidad'];
$criptomoneda = $_POST['criptomoneda'];
$red = $_POST['red'];
$wallet = $_POST['wallet'];

// Validar datos aquí si es necesario

// Insertar la nueva solicitud de retiro en la base de datos
$queryInsertRetiro = "INSERT INTO retiros (usuario_id, cantidad, criptomoneda, red, wallet, estado, fecha_solicitud) VALUES (:usuario_id, :cantidad, :criptomoneda, :red, :wallet, 'pendiente', NOW())";
$stmtInsertRetiro = $pdo->prepare($queryInsertRetiro);
$stmtInsertRetiro->execute([
    'usuario_id' => $userId,
    'cantidad' => $cantidad,
    'criptomoneda' => $criptomoneda,
    'red' => $red,
    'wallet' => $wallet
]);

// Registrar la operación en el historial
$queryInsertHistorial = "INSERT INTO historial (user_id, cantidad, sistema, tipo_operacion, fecha) VALUES (:user_id, :cantidad, 'retiro', 'retiro', NOW())";
$stmtInsertHistorial = $pdo->prepare($queryInsertHistorial);
$stmtInsertHistorial->execute([
    'user_id' => $userId,
    'cantidad' => $cantidad

]);

// Después de procesar la solicitud, configura la variable de sesión 'pendiente_aprobacion'
$_SESSION['pendiente_aprobacion'] = true;

// Redirigir de vuelta a la página de retiro de fondos
header("Location: retiros.php");
exit;
?>
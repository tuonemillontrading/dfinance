<?php
session_start();
require 'config.php';

// Verificar si el usuario está logueado
if (!isset($_SESSION['usuario_id'])) {
    echo json_encode(["error" => "Usuario no logueado"]);
    exit();
}

// Obtener el ID del usuario desde la sesión
$usuario_id = $_SESSION['usuario_id'];

// Verificar si se ha pasado un parámetro tipo
if (!isset($_GET['tipo'])) {
    echo json_encode(["error" => "Tipo no especificado"]);
    exit();
}

$tipo = $_GET['tipo'];

// Consulta SQL según el tipo de ganancia
if ($tipo == 'trading_ia') {
    $sql = "SELECT DATE(fecha) AS fecha, SUM(cantidad) AS ganancia 
            FROM historial 
            WHERE sistema = 'trading_ia' AND tipo_operacion = 'ganancia' AND user_id = ? 
            GROUP BY DATE(fecha) 
            ORDER BY fecha ASC";
} else if ($tipo == 'validator_node') {
    $sql = "SELECT DATE(fecha) AS fecha, SUM(cantidad) AS ganancia 
            FROM historial 
            WHERE sistema = 'validator_node' AND tipo_operacion = 'ganancia' AND user_id = ? 
            GROUP BY DATE(fecha) 
            ORDER BY fecha ASC";
} else {
    echo json_encode(["error" => "Tipo inválido"]);
    exit();
}

// Crear la conexión
$pdo = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Preparar y ejecutar la consulta
$stmt = $pdo->prepare($sql);
$stmt->execute([$usuario_id]);
$data = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Devolver los datos en formato JSON
echo json_encode($data);
?>
<?php
// Configuración de la base de datos
$host = 'localhost';  // Dirección del servidor de la base de datos
$db = 'dfinance';  // Nombre de la base de datos
$user = 'root';  // Reemplaza 'root' con tu nombre de usuario de la base de datos
$pass = '';  // Reemplaza '' con tu contraseña de la base de datos

try {
    // Crear una nueva conexión PDO
    $pdo = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
    // Configurar PDO para que lance excepciones en caso de error
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    // En caso de error, mostrar un mensaje y detener la ejecución del script
    die("Conexión fallida: " . $e->getMessage());
}
?>
<?php
include 'config.php';
include 'auth.php';

// Verificar si el usuario está autenticado y es administrador
check_admin();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $userId = $_POST['id'];

    try {
        $stmt = $pdo->prepare("DELETE FROM usuarios WHERE id = :id");
        $stmt->execute(['id' => $userId]);
        echo json_encode(['success' => true]);
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
}
?>
<?php
include 'config.php'; // Incluir el archivo de configuración

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['usuario_id'])) {
    die("Error: No has iniciado sesión.");
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $retiro_id = $_POST['retiro_id'];

    // Validar que el ID del retiro es un número
    if (!is_numeric($retiro_id)) {
        die("Error: ID de retiro no válido.");
    }

    try {
        // Iniciar una transacción
        $pdo->beginTransaction();

        // Actualizar el estado del retiro a 'aprobado'
        $queryAprobarRetiro = "UPDATE retiros SET estado = 'aprobado' WHERE id = :retiro_id";
        $stmtAprobarRetiro = $pdo->prepare($queryAprobarRetiro);
        $stmtAprobarRetiro->execute(['retiro_id' => $retiro_id]);

        // Mover el retiro al historial
        $queryHistorial = "INSERT INTO historial_retiros (usuario_id, cantidad, criptomoneda, red, wallet, estado, fecha_solicitud)
                           SELECT usuario_id, cantidad, criptomoneda, red, wallet, estado, fecha_solicitud FROM retiros WHERE id = :retiro_id";
        $stmtHistorial = $pdo->prepare($queryHistorial);
        $stmtHistorial->execute(['retiro_id' => $retiro_id]);

        // Eliminar el retiro de la tabla original
        $queryEliminarRetiro = "DELETE FROM retiros WHERE id = :retiro_id";
        $stmtEliminarRetiro = $pdo->prepare($queryEliminarRetiro);
        $stmtEliminarRetiro->execute(['retiro_id' => $retiro_id]);

        // Confirmar la transacción
        $pdo->commit();

        // Mensaje de éxito
        $_SESSION['mensaje'] = "Retiro aprobado y movido al historial con éxito.";
    } catch (Exception $e) {
        // Revertir la transacción en caso de error
        $pdo->rollBack();
        // Mensaje de error
        $_SESSION['mensaje'] = "Error al aprobar el retiro: " . $e->getMessage();
    }

    header('Location: retiros_clientes.php');
    exit;
}
?>
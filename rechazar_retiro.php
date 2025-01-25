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

        // Obtener la información del retiro
        $queryRetiro = "SELECT * FROM retiros WHERE id = :retiro_id";
        $stmtRetiro = $pdo->prepare($queryRetiro);
        $stmtRetiro->execute(['retiro_id' => $retiro_id]);
        $retiro = $stmtRetiro->fetch(PDO::FETCH_ASSOC);

        if ($retiro) {
            $usuario_id = $retiro['usuario_id'];
            $cantidad = $retiro['cantidad'];

            // Devolver el capital al usuario
            $queryDevolverCapital = "UPDATE usuarios SET 
                ganancias_validator_x = ganancias_validator_x + :cantidad
                WHERE id = :usuario_id";
            $stmtDevolverCapital = $pdo->prepare($queryDevolverCapital);
            $stmtDevolverCapital->execute(['cantidad' => $cantidad, 'usuario_id' => $usuario_id]);

            // Eliminar la solicitud de retiro
            $queryEliminarRetiro = "DELETE FROM retiros WHERE id = :retiro_id";
            $stmtEliminarRetiro = $pdo->prepare($queryEliminarRetiro);
            $stmtEliminarRetiro->execute(['retiro_id' => $retiro_id]);

            // Confirmar la transacción
            $pdo->commit();

            // Mensaje de éxito
            $_SESSION['mensaje'] = "Retiro rechazado y capital devuelto al cliente con éxito.";
        } else {
            $_SESSION['mensaje'] = "Error: No se encontró la solicitud de retiro.";
        }
    } catch (Exception $e) {
        // Revertir la transacción en caso de error
        $pdo->rollBack();
        // Mensaje de error
        $_SESSION['mensaje'] = "Error al rechazar el retiro: " . $e->getMessage();
    }

    header('Location: retiros_clientes.php');
    exit;
}
?>
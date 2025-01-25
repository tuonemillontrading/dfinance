<?php
require 'config.php';

/**
 * Obtener todos los usuarios
 * @param PDO $pdo
 * @return array
 */
function getUsuarios($pdo) {
    $stmt = $pdo->prepare("SELECT id, nombre, correo, rol, capital_validator_x, capital_trading_ia FROM usuarios");
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

/**
 * Obtener un usuario por ID
 * @param PDO $pdo
 * @param int $id
 * @return array|false
 */
function getUsuarioById($pdo, $id) {
    $stmt = $pdo->prepare("SELECT id, nombre, correo, rol, capital_validator_x, capital_trading_ia FROM usuarios WHERE id = :id");
    $stmt->execute(['id' => $id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

/**
 * Añadir un nuevo usuario
 * @param PDO $pdo
 * @param string $nombre
 * @param string $correo
 * @param string $contrasena
 * @param float $capital_validator_x
 * @param float $capital_trading_ia
 * @param string $rol
 * @return bool
 */
function addUsuario($pdo, $nombre, $correo, $contrasena, $capital_validator_x, $capital_trading_ia, $rol) {
    // Validar si el correo ya existe
    $stmt = $pdo->prepare("SELECT id FROM usuarios WHERE correo = :correo");
    $stmt->execute(['correo' => $correo]);
    if ($stmt->fetch()) {
        return false; // El correo ya existe
    }

    $hashed_password = password_hash($contrasena, PASSWORD_BCRYPT);
    $stmt = $pdo->prepare("INSERT INTO usuarios (nombre, correo, contrasena, capital_validator_x, capital_trading_ia, rol) VALUES (:nombre, :correo, :contrasena, :capital_validator_x, :capital_trading_ia, :rol)");
    return $stmt->execute(['nombre' => $nombre, 'correo' => $correo, 'contrasena' => $hashed_password, 'capital_validator_x' => $capital_validator_x, 'capital_trading_ia' => $capital_trading_ia, 'rol' => $rol]);
}

/**
 * Actualizar un usuario existente
 * @param PDO $pdo
 * @param int $id
 * @param string $nombre
 * @param string $correo
 * @param float $capital_validator_x
 * @param float $capital_trading_ia
 * @param string $rol
 * @return bool
 */
function updateUsuario($pdo, $id, $nombre, $correo, $capital_validator_x, $capital_trading_ia, $rol) {
    $stmt = $pdo->prepare("UPDATE usuarios SET nombre = :nombre, correo = :correo, capital_validator_x = :capital_validator_x, capital_trading_ia = :capital_trading_ia, rol = :rol WHERE id = :id");
    return $stmt->execute(['id' => $id, 'nombre' => $nombre, 'correo' => $correo, 'capital_validator_x' => $capital_validator_x, 'capital_trading_ia' => $capital_trading_ia, 'rol' => $rol]);
}

/**
 * Eliminar un usuario
 * @param PDO $pdo
 * @param int $id
 * @return bool
 */
function deleteUsuario($pdo, $id) {
    try {
        // Eliminar registros relacionados en la tabla historial
        $stmt = $pdo->prepare("DELETE FROM historial WHERE user_id = :id");
        $stmt->execute([':id' => $id]);

        // Eliminar el usuario
        $stmt = $pdo->prepare("DELETE FROM usuarios WHERE id = :id");
        return $stmt->execute([':id' => $id]);
    } catch (PDOException $e) {
        return false;
    }
}

/**
 * Obtener todos los depósitos pendientes
 * @param PDO $pdo
 * @return array
 */
function getDepositosPendientes($pdo) {
    $stmt = $pdo->prepare("SELECT * FROM depositos WHERE estado = 'pendiente' ORDER BY fecha DESC");
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

/**
 * Aprobar un depósito y abonar al sistema
 * @param PDO $pdo
 * @param int $id
 * @return array|bool
 */
function aprobarDeposito($pdo, $id) {
    $pdo->beginTransaction();
    try {
        // Obtener detalles del depósito
        $stmt = $pdo->prepare("SELECT usuario_id, monto, sistema FROM depositos WHERE id = :id AND estado = 'pendiente'");
        $stmt->execute(['id' => $id]);
        $deposito = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$deposito) {
            throw new Exception('No se encontraron detalles del depósito o el depósito ya fue procesado.');
        }

        // Actualizar el estado del depósito a 'aprobado'
        $stmt = $pdo->prepare("UPDATE depositos SET estado = 'aprobado' WHERE id = :id");
        if (!$stmt->execute(['id' => $id])) {
            throw new Exception('Error al actualizar el estado del depósito.');
        }

        // Obtener detalles del usuario
        $stmt = $pdo->prepare("SELECT nombre FROM usuarios WHERE id = :usuario_id");
        $stmt->execute(['usuario_id' => $deposito['usuario_id']]);
        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$usuario) {
            throw new Exception('No se encontraron detalles del usuario.');
        }

        // Abonar el monto al sistema correspondiente del usuario
        if ($deposito['sistema'] === 'validator_node') {
            $stmt = $pdo->prepare("UPDATE usuarios SET capital_validator_x = capital_validator_x + :monto WHERE id = :usuario_id");
        } elseif ($deposito['sistema'] === 'trading_ia') {
            $stmt = $pdo->prepare("UPDATE usuarios SET capital_trading_ia = capital_trading_ia + :monto WHERE id = :usuario_id");
        } else {
            throw new Exception('Sistema desconocido.');
        }
        if (!$stmt->execute(['monto' => $deposito['monto'], 'usuario_id' => $deposito['usuario_id']])) {
            throw new Exception('Error al actualizar el saldo del usuario.');
        }

        // Registrar en el historial
        $stmt = $pdo->prepare("INSERT INTO historial (user_id, tipo_operacion, sistema, cantidad, fecha) VALUES (:usuario_id, 'deposito', :sistema, :monto, NOW())");
        if (!$stmt->execute(['usuario_id' => $deposito['usuario_id'], 'sistema' => $deposito['sistema'], 'monto' => $deposito['monto']])) {
            throw new Exception('Error al registrar en el historial.');
        }

        $pdo->commit();
        return ['status' => true, 'usuario' => $usuario['nombre']];
    } catch (Exception $e) {
        $pdo->rollBack();
        error_log("Error al aprobar el depósito: " . $e->getMessage());
        return ['status' => false, 'error' => $e->getMessage()];
    }
}

/**
 * Rechazar un depósito
 * @param PDO $pdo
 * @param int $id
 * @return bool
 */
function rechazarDeposito($pdo, $id) {
    $stmt = $pdo->prepare("UPDATE depositos SET estado = 'rechazado' WHERE id = :id");
    return $stmt->execute(['id' => $id]);
}
?>
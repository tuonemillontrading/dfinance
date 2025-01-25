<?php
// Verificar si una sesión ya está activa
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
require 'config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $userId = $_SESSION['usuario_id'];
    $cantidad = floatval($_POST['cantidad']);
    $plan = $_POST['plan'];
    
    // Obtener las ganancias y capital del usuario
    $stmt = $pdo->prepare("SELECT capital_validator_x, capital_trading_ia, ganancias_validator_x, ganancias_trading_ia FROM usuarios WHERE id = :id");
    $stmt->execute(['id' => $userId]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user === false) {
        die("Error: No se pudieron obtener las ganancias del usuario.");
    }

    $totalGanancias = $user['ganancias_validator_x'] + $user['ganancias_trading_ia'];

    // Verificar si la cantidad es válida
    if ($cantidad <= 0) {
        die("Error: La cantidad debe ser mayor a 0 USDT.");
    }
    
    // Verificar si el usuario tiene suficientes ganancias disponibles
    if ($cantidad > $totalGanancias) {
        die("Error: No tienes suficientes ganancias disponibles para esta inversión.");
    }

    // Actualizar las ganancias y el capital según el plan de inversión seleccionado
    $nuevasGananciasValidatorX = $user['ganancias_validator_x'];
    $nuevasGananciasTradingIA = $user['ganancias_trading_ia'];
    
    if ($cantidad > $nuevasGananciasValidatorX) {
        $restante = $cantidad - $nuevasGananciasValidatorX;
        $nuevasGananciasValidatorX = 0;
        $nuevasGananciasTradingIA -= $restante;
    } else {
        $nuevasGananciasValidatorX -= $cantidad;
    }

    // Actualizar el capital en el plan elegido
    if ($plan == 'validator_node') {
        $nuevoCapitalValidatorX = $user['capital_validator_x'] + $cantidad;
        $nuevoCapitalTradingIA = $user['capital_trading_ia'];
    } elseif ($plan == 'trading_ia') {
        $nuevoCapitalTradingIA = $user['capital_trading_ia'] + $cantidad;
        $nuevoCapitalValidatorX = $user['capital_validator_x'];
    } else {
        die("Error: Plan de inversión no válido.");
    }

    // Registrar la reinversión en la tabla de historial como deposito
    $stmt_historial = $pdo->prepare("INSERT INTO historial (user_id, tipo_operacion, sistema, cantidad, fecha) VALUES (?, 'deposito', ?, ?, NOW())");
    if (!$stmt_historial->execute([$userId, $plan, $cantidad])) {
        die("Error al insertar en historial: " . implode(" ", $stmt_historial->errorInfo()));
    }

    // Actualizar los saldos en la base de datos
    $stmt = $pdo->prepare("UPDATE usuarios SET capital_validator_x = :capital_validator_x, capital_trading_ia = :capital_trading_ia, ganancias_validator_x = :ganancias_validator_x, ganancias_trading_ia = :ganancias_trading_ia WHERE id = :id");
    $stmt->execute([
        'capital_validator_x' => $nuevoCapitalValidatorX,
        'capital_trading_ia' => $nuevoCapitalTradingIA,
        'ganancias_validator_x' => $nuevasGananciasValidatorX,
        'ganancias_trading_ia' => $nuevasGananciasTradingIA,
        'id' => $userId
    ]);

    // Redirigir al usuario a la página de reinversión con un mensaje de éxito
    header("Location: restake.php?success=1");
    exit();
}
?>
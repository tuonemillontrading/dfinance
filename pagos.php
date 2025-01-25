<?php
session_start();
require 'config.php';
require 'functions.php';

// Verificar si el usuario está autenticado
if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit;
}

$mensaje_validator = ''; // Variable para almacenar el mensaje de Validator X
$mensaje_trading = ''; // Variable para almacenar el mensaje de Trading IA

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['percentage_validator'])) {
        $porcentaje_validator = $_POST['percentage_validator'];

        // Consulta para obtener todos los usuarios con capital en Validator X
        $stmt = $pdo->prepare("SELECT id, capital_validator_x FROM usuarios WHERE capital_validator_x > 0");
        $stmt->execute();
        $usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($usuarios as $usuario) {
            $usuario_id = $usuario['id'];
            $capital = $usuario['capital_validator_x'];
            $ganancia = $capital * ($porcentaje_validator / 100);

            // Actualizar las ganancias de Validator X en la tabla de usuarios
            $stmt_update = $pdo->prepare("UPDATE usuarios SET ganancias_validator_x = ganancias_validator_x + ? WHERE id = ?");
            $stmt_update->execute([$ganancia, $usuario_id]);

            // Registrar la ganancia en la tabla de historial
            $stmt_historial = $pdo->prepare("INSERT INTO historial (user_id, tipo_operacion, sistema, cantidad, fecha) VALUES (?, 'ganancia', 'validator_node', ?, NOW())");
            if (!$stmt_historial->execute([$usuario_id, $ganancia])) {
                die("Error al insertar en historial: " . implode(" ", $stmt_historial->errorInfo()));
            }
        }

        // Asignar mensaje de pago completado
        $mensaje_validator = "Pago de ganancias completado con éxito para Validator X.";
    }

    if (isset($_POST['percentage_trading'])) {
        $porcentaje_trading = $_POST['percentage_trading'];

        // Consulta para obtener todos los usuarios con capital en Trading IA
        $stmt = $pdo->prepare("SELECT id, capital_trading_ia FROM usuarios WHERE capital_trading_ia > 0");
        $stmt->execute();
        $usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($usuarios as $usuario) {
            $usuario_id = $usuario['id'];
            $capital = $usuario['capital_trading_ia'];
            $ganancia = $capital * ($porcentaje_trading / 100);

            // Actualizar las ganancias de Trading IA en la tabla de usuarios
            $stmt_update = $pdo->prepare("UPDATE usuarios SET ganancias_trading_ia = ganancias_trading_ia + ? WHERE id = ?");
            $stmt_update->execute([$ganancia, $usuario_id]);

            // Registrar la ganancia en la tabla de historial
            $stmt_historial = $pdo->prepare("INSERT INTO historial (user_id, tipo_operacion, sistema, cantidad, fecha) VALUES (?, 'ganancia', 'trading_ia', ?, NOW())");
            if (!$stmt_historial->execute([$usuario_id, $ganancia])) {
                die("Error al insertar en historial: " . implode(" ", $stmt_historial->errorInfo()));
            }
        }

        // Asignar mensaje de pago completado
        $mensaje_trading = "Pago de ganancias completado con éxito para Trading IA.";
    }
}
?>

<?php include './partials/layouts/layoutTop.php'; ?>

<div class="dashboard-main-body">
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
        <h6 class="fw-medium mb-0">Pago de Ganancias</h6>
        <ul class="d-flex align-items-center gap-2">
            <li class="fw-medium">
                <a href="index.php" class="d-flex align-items-center gap-1 hover-text-primary">
                    <iconify-icon icon="solar:home-smile-angle-outline" class="icon text-lg"></iconify-icon>
                    Dashboard
                </a>
            </li>
            <li>-</li>
            <li class="fw-medium">Pago de Ganancias</li>
        </ul>
    </div>

    <div class="card h-100 p-0 radius-12">
        <div class="card-body p-24">
            <div class="form-container">
                <h6>Pago de Ganancias - Validator X</h6>
                <?php if ($mensaje_validator): ?>
                    <div class="alert alert-info"><?php echo $mensaje_validator; ?></div>
                <?php endif; ?>
                <form action="" method="POST" id="form_validator">
                    <div class="form-group">
                        <label for="percentage_validator">Porcentaje de Ganancia:</label>
                        <input type="number" id="percentage_validator" name="percentage_validator" min="0" max="100" step="0.01" required oninput="calculateTotal('validator')">
                    </div>
                    <div class="form-group">
                        <label>Total a Pagar:</label>
                        <p id="total_validator">0</p>
                    </div>
                    <div class="form-group" style="margin-top: 20px; text-align: center;">
                        <button type="submit">Pagar Ganancias Validator X</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="card h-100 p-0 radius-12">
        <div class="card-body p-24">
            <div class="form-container">
                <h6>Pago de Ganancias - Trading IA</h6>
                <?php if ($mensaje_trading): ?>
                    <div class="alert alert-info"><?php echo $mensaje_trading; ?></div>
                <?php endif; ?>
                <form action="" method="POST" id="form_trading">
                    <div class="form-group">
                        <label for="percentage_trading">Porcentaje de Ganancia:</label>
                        <input type="number" id="percentage_trading" name="percentage_trading" min="0" max="100" step="0.01" required oninput="calculateTotal('trading')">
                    </div>
                    <div class="form-group">
                        <label>Total a Pagar:</label>
                        <p id="total_trading">0</p>
                    </div>
                    <div class="form-group" style="margin-top: 20px; text-align: center;">
                        <button type="submit">Pagar Ganancias Trading IA</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include './partials/layouts/layoutBottom.php'; ?>

<style>
    .form-container {
        max-width: 700px;
        margin: auto;
        padding: 20px;
        border: 1px solid var(--border-color, #ccc);
        border-radius: 10px;
        background-color: #1B2431;
    }

    .form-container h6 {
        text-align: center;
        font-size: 1.25rem;
    }

    .form-group {
        margin-bottom: 15px;
    }

    .form-group label {
        display: block;
        margin-bottom: 5px;
    }

    .form-group .input-group {
        display: flex;
        align-items: center;
        position: relative;
    }

    .form-group input,
    .form-group select {
        width: 100%;
        padding: 8px;
        box-sizing: border-box;
        border: 1px solid var(--input-border-color, #ccc);
        border-radius: 5px;
        background-color: #273142;
        color: var(--input-text-color, #fff);
    }

    .form-group input[readonly] {
        background-color: #273142;
    }

    .copy-icon {
        position: absolute;
        right: 10px;
        cursor: pointer;
        display: flex;
        align-items: center;
        padding: 8px;
        background-color: transparent;
        color: var(--button-text-color, #fff);
    }

    .form-group button {
        width: 100%;
        padding: 10px;
        border: none;
        border-radius: 5px;
        background-color: var(--button-background-color, #007bff);
        color: var(--button-text-color, #fff);
        cursor: pointer;
    }

    .form-group button:disabled {
        background-color: var(--button-disabled-background-color, #6c757d);
        cursor: not-allowed;
    }

    .note {
        background-color: #fdd;
        padding: 10px;
        border: 1px solid #f00;
        border-radius: 5px;
        margin-top: 15px;
        color: #000;
        font-size: 0.875rem;
        white-space: normal;
        word-wrap: break-word;
    }

    .copy-confirmation {
        display: flex;
        align-items: center;
        margin-top: 10px;
        color: green;
        font-size: 0.9rem;
    }

    .qr-image {
        width: 100px;
        height: 100px;
    }

    @media (prefers-color-scheme: dark) {
        body {
            --background-color: #121212;
            --text-color: #ffffff;
            --border-color: #333;
            --input-border-color: #555;
            --button-background-color: #007bff;
            --button-text-color: #ffffff;
            --button-disabled-background-color: #6c757d;
        }
        .form-container {
            background-color: #1B2431;
        }
        .form-group input,
        .form-group select,
        .form-group input[readonly] {
            background-color: #273142;
        }
        .copy-icon {
            color: var(--button-text-color, #fff);
        }
        .note {
            color: #000;
        }
    }
</style>

<script>
    function calculateTotal(system) {
        let percentageInput, totalLabel;
        if (system === 'validator') {
            percentageInput = document.getElementById('percentage_validator').value;
            totalLabel = document.getElementById('total_validator');
        } else if (system === 'trading') {
            percentageInput = document.getElementById('percentage_trading').value;
            totalLabel = document.getElementById('total_trading');
        }

        if (percentageInput) {
            const formData = new FormData();
            formData.append('system', system);
            formData.append('percentage', percentageInput);

            fetch('calculate_total.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                const totalToPay = data.totalToPay;
                totalLabel.textContent = totalToPay.toFixed(2);
            });
        } else {
            totalLabel.textContent = '0';
        }
    } 
</script>
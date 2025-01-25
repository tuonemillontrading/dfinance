<?php include './partials/layouts/layoutTop.php'; ?>

<?php
// Verificar si una sesión ya está activa
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Verificar si 'usuario_id' está configurado en la sesión
if (!isset($_SESSION['usuario_id'])) {
    die("Error: No has iniciado sesión.");
}

require 'config.php';

// Obtener el ID del usuario desde la sesión
$userId = $_SESSION['usuario_id'];

// Preparar y ejecutar la consulta SQL para obtener las ganancias del usuario
$stmt = $pdo->prepare("SELECT ganancias_validator_x, ganancias_trading_ia FROM usuarios WHERE id = :id");
$stmt->execute(['id' => $userId]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if ($user === false) {
    die("Error: No se pudieron obtener las ganancias del usuario.");
}

// Calcular el total de las ganancias disponibles
$totalGanancias = $user['ganancias_validator_x'] + $user['ganancias_trading_ia'];
?>

<div class="dashboard-main-body">
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
        <h6 class="fw-semibold mb-0">Reinversión</h6>
        <ul class="d-flex align-items-center gap-2">
            <li class="fw-medium">
                <a href="home.php" class="d-flex align-items-center gap-1 hover-text-primary">
                    <iconify-icon icon="solar:home-smile-angle-outline" class="icon text-lg"></iconify-icon>
                    Inicio
                </a>
            </li>
            <li>-</li>
            <li class="fw-medium">Reinversión</li>
        </ul>
    </div>
    
    <!-- Botón de historial de restakes -->
    <div class="d-flex justify-content-end mb-3">
        <a href="historial_restake.php" class="btn btn-secondary btn-sm">Historial de Restakes</a>
    </div>

    <h3></h3>
    <p>Realiza una inversión utilizando tus ganancias disponibles.</p>

    <?php if (isset($_GET['success'])): ?>
        <div class="alert alert-success">
            Inversión realizada con éxito.
        </div>
    <?php endif; ?>

    <div class="mb-24">
        <label for="total-ganancias" class="form-label fw-semibold">Ganancias Totales Disponibles</label>
        <input type="text" id="total-ganancias" class="form-control" value="<?php echo number_format($totalGanancias, 2); ?> USDT" readonly>
    </div>

    <form action="procesar_reinversion.php" method="POST" onsubmit="return validarCantidad()">
        <div class="mb-24">
            <label for="cantidad" class="form-label fw-semibold">Cantidad a Invertir</label>
            <input type="number" id="cantidad" name="cantidad" class="form-control" placeholder="Introduce la cantidad" required>
        </div>

        <div class="mb-24">
            <label for="plan" class="form-label fw-semibold">Plan de Inversión</label>
            <select id="plan" name="plan" class="form-control" required>
                <option value="validator_node">Validator Node</option>
                <option value="trading_ia">Trading IA</option>
            </select>
        </div>

        <div class="alert alert-info">
            Recuerde que puede consultar el estado de sus inversiones en la sección de historial. Si tiene algún problema, por favor, contacte con soporte.
        </div>

        <button type="submit" class="btn btn-primary">Realizar Inversión</button>
    </form>
</div>

<script>
function validarCantidad() {
    var cantidad = document.getElementById('cantidad').value;
    var totalGanancias = <?php echo $totalGanancias; ?>;

    if (cantidad <= 0) {
        alert('La cantidad debe ser mayor a 0 USDT.');
        return false;
    }

    if (cantidad > totalGanancias) {
        alert('No tienes suficientes ganancias disponibles para esta inversión.');
        return false;
    }

    return true;
}
</script>

<?php include './partials/layouts/layoutBottom.php'; ?>
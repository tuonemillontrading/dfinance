<?php include './partials/layouts/layoutTop.php'; ?>
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

// Obtener el saldo disponible del usuario
$query = "SELECT saldo_disponible FROM usuarios WHERE id = :user_id";
$stmt = $pdo->prepare($query);
$stmt->execute(['user_id' => $userId]);
$saldo = $stmt->fetchColumn();
?>

<div class="dashboard-main-body">
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
        <h6 class="fw-semibold mb-0">Retirar Fondos</h6>
        <ul class="d-flex align-items-center gap-2">
            <li class="fw-medium">
                <a href="index.php" class="d-flex align-items-center gap-1 hover-text-primary">
                    <iconify-icon icon="solar:home-smile-angle-outline" class="icon text-lg"></iconify-icon>
                    Dashboard
                </a>
            </li>
            <li>-</li>
            <li class="fw-medium">Retirar Fondos</li>
        </ul>
    </div>

    <div class="mb-24">
        <h2>¡Disfruta de tus ganancias!</h2>
        <p>Recuerda que los retiros se realizan desde tu Saldo disponible. Asegúrate de tener saldo suficiente antes de iniciar la transacción.</p>

        <h3>Retiro por transferencia (Colombia)</h3>
        
        <form action="procesar_retiro.php" method="POST">
            <div class="form-group">
                <label for="cantidad">Cantidad a Retirar (USD)</label>
                <input type="number" id="cantidad" name="cantidad" class="form-control" min="50" max="100000" step="0.01" required>
            </div>
            <div class="form-group">
                <label for="criptomoneda">Criptomoneda</label>
                <select id="criptomoneda" name="criptomoneda" class="form-control" required>
                    <option value="USDT">USDT</option>
                    <option value="USDC">USDC</option>
                </select>
            </div>
            <div class="form-group">
                <label for="red">Red</label>
                <select id="red" name="red" class="form-control" required>
                    <option value="ERC20">ERC20</option>
                    <option value="TRC20">TRC20</option>
                    <option value="BEP20">BEP20</option>
                </select>
            </div>
            <div class="form-group">
                <label for="saldo">Saldo Disponible</label>
                <input type="text" id="saldo" class="form-control" value="<?php echo number_format($saldo, 2); ?> USD" readonly>
            </div>
            <div class="form-group">
                <label for="cargo">Cargo</label>
                <input type="text" id="cargo" class="form-control" value="0 USD" readonly>
            </div>
            <div class="form-group">
                <label for="pagable">Pagable</label>
                <input type="text" id="pagable" class="form-control" value="0.00 USD" readonly>
            </div>
            <div class="alert alert-info">
                Verifica que la información sea correcta. Una vez solicitado el retiro, será acreditado a tu billetera en un lapso de 1 a 24 horas. El procedimiento es rápido, fácil y seguro.
            </div>
            <button type="submit" class="btn btn-primary">Solicitar Retiro</button>
        </form>
    </div>
</div>

<script>
document.getElementById('cantidad').addEventListener('input', function() {
    var cantidad = parseFloat(this.value) || 0;
    var pagable = cantidad.toFixed(2) + ' USD';
    document.getElementById('pagable').value = pagable;
});
</script>

<?php include './partials/layouts/layoutBottom.php'; ?>
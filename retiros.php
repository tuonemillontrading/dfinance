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

// Obtener el capital bloqueado (suma de capital_validator_x y capital_trading_ia)
$queryCapitalBloqueado = "SELECT (capital_validator_x + capital_trading_ia) AS capital_bloqueado FROM usuarios WHERE id = :user_id";
$stmtCapitalBloqueado = $pdo->prepare($queryCapitalBloqueado);
$stmtCapitalBloqueado->execute(['user_id' => $userId]);
$capitalBloqueado = $stmtCapitalBloqueado->fetchColumn() ?: 0.00;

// Obtener las ganancias disponibles (suma de ganancias_validator_x y ganancias_trading_ia)
$queryGanancias = "SELECT (ganancias_validator_x + ganancias_trading_ia) AS total_ganancias FROM usuarios WHERE id = :user_id";
$stmtGanancias = $pdo->prepare($queryGanancias);
$stmtGanancias->execute(['user_id' => $userId]);
$ganancias = $stmtGanancias->fetchColumn() ?: 0.00;

// Verificar si hay un mensaje de pendiente de aprobación
$pendienteAprobacion = isset($_SESSION['pendiente_aprobacion']) ? $_SESSION['pendiente_aprobacion'] : false;
if ($pendienteAprobacion) {
    unset($_SESSION['pendiente_aprobacion']); // Limpiar la variable de sesión después de mostrar el mensaje
}
?>

<div class="dashboard-main-body">
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
        <h6 class="fw-semibold mb-0">Retirar Fondos</h6>
        <ul class="d-flex align-items-center gap-2">
            <li class="fw-medium">
                <a href="home.php" class="d-flex align-items-center gap-1 hover-text-primary">
                    <iconify-icon icon="solar:home-smile-angle-outline" class="icon text-lg"></iconify-icon>
                    Dashboard
                </a>
            </li>
            <li>-</li>
            <li class="fw-medium">Retirar Fondos</li>
        </ul>
    </div>

    <div class="mb-24">
        <?php if ($pendienteAprobacion): ?>
            <div class="alert alert-warning" style="padding: 15px; font-size: 1em;">
                Pendiente de aprobación. Tu solicitud de retiro está en proceso de revisión.
            </div>
        <?php endif; ?>
        <form action="procesar_retiro.php" method="POST">
            <div class="form-group mb-3">
                <label for="cantidad">Cantidad a Retirar (USD) (Ganancias disponibles: <?php echo number_format($ganancias, 2); ?> USDT)</label>
                <input type="number" id="cantidad" name="cantidad" class="form-control" min="50" max="<?php echo $ganancias; ?>" step="0.01" required>
            </div>
            <div class="form-group mb-3">
                <label for="criptomoneda">Criptomoneda</label>
                <select id="criptomoneda" name="criptomoneda" class="form-control" required>
                    <option value="" selected disabled>Seleccione una opción</option>
                    <option value="USDT">USDT</option>
                    <option value="USDC">USDC</option>
                </select>
            </div>
            <div class="form-group mb-3" id="red-group" style="display: none;">
                <label for="red">Red</label>
                <select id="red" name="red" class="form-control" required disabled>
                    <option value="" selected disabled>Seleccione una red</option>
                    <option value="TRC20" class="usdt">TRC20</option>
                    <option value="ERC20" class="usdt">ERC20</option>
                    <option value="BEP20" class="usdt">BEP20</option>
                    <option value="Base Mainnet" class="usdc">Base Mainnet</option>
                </select>
            </div>
            <div class="form-group mb-3" id="wallet-group" style="display: none;">
                <label for="wallet">Dirección de la Wallet de Retiro</label>
                <input type="text" id="wallet" name="wallet" class="form-control" required>
            </div>
            <div class="form-group mb-3">
                <label for="capital_bloqueado">Capital Bloqueado</label>
                <input type="text" id="capital_bloqueado" class="form-control" value="<?php echo number_format($capitalBloqueado, 2); ?> USD" readonly>
            </div>
            <div class="alert alert-info" style="padding: 15px; font-size: 0.8em;">
                Verifica que la información sea correcta. Una vez solicitado el retiro, será acreditado a tu billetera en un lapso de 1 a 24 horas. El procedimiento es rápido, fácil y seguro.
            </div>
            <button type="submit" class="btn btn-primary">Solicitar Retiro</button>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const criptomonedaSelect = document.getElementById('criptomoneda');
    const redGroup = document.getElementById('red-group');
    const walletGroup = document.getElementById('wallet-group');
    const redSelect = document.getElementById('red');

    criptomonedaSelect.addEventListener('change', function() {
        const selectedCrypto = criptomonedaSelect.value;
        redSelect.disabled = false;

        // Ocultar todas las opciones primero
        for (const option of redSelect.options) {
            option.style.display = 'none';
        }

        // Mostrar opciones basadas en la criptomoneda seleccionada
        if (selectedCrypto === 'USDT') {
            for (const option of redSelect.options) {
                if (option.classList.contains('usdt')) {
                    option.style.display = 'block';
                }
            }
        } else if (selectedCrypto === 'USDC') {
            for (const option of redSelect.options) {
                if (option.classList.contains('usdc')) {
                    option.style.display = 'block';
                }
            }
        }

        // Resetear la opción seleccionada
        redSelect.value = '';

        // Mostrar los grupos de red y wallet
        redGroup.style.display = 'block';
        walletGroup.style.display = 'block';
    });
});
</script>

<?php include './partials/layouts/layoutBottom.php'; ?>

<style>
    .form-group select {
        background-color: transparent;
        color: inherit;
    }
</style>
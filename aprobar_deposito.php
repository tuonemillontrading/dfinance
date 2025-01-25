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

// Obtener todas las solicitudes de retiro pendientes
$queryRetirosPendientes = "SELECT * FROM retiros WHERE estado = 'pendiente'";
$stmtRetirosPendientes = $pdo->prepare($queryRetirosPendientes);
$stmtRetirosPendientes->execute();
$retirosPendientes = $stmtRetirosPendientes->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="dashboard-main-body">
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
        <h6 class="fw-semibold mb-0">Solicitudes de Retiro Pendientes</h6>
        <ul class="d-flex align-items-center gap-2">
            <li class="fw-medium">
                <a href="index.php" class="d-flex align-items-center gap-1 hover-text-primary">
                    <iconify-icon icon="solar:home-smile-angle-outline" class="icon text-lg"></iconify-icon>
                    Dashboard
                </a>
            </li>
            <li>-</li>
            <li class="fw-medium">Solicitudes de Retiro</li>
        </ul>
    </div>

    <div class="card h-100 p-0 radius-12">
        <div class="card-body p-24">
            <div class="table-responsive scroll-sm">
                <table class="table bordered-table sm-table mb-0">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th class="text-center">Usuario ID</th>
                            <th class="text-center">Cantidad</th>
                            <th class="text-center">Criptomoneda</th>
                            <th class="text-center">Red</th>
                            <th class="text-center">Wallet</th>
                            <th class="text-center">Fecha Solicitud</th>
                            <th class="text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($retirosPendientes as $retiro): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($retiro['id']); ?></td>
                            <td class="text-center"><?php echo htmlspecialchars($retiro['usuario_id']); ?></td>
                            <td class="text-center"><?php echo number_format($retiro['cantidad'], 2); ?></td>
                            <td class="text-center"><?php echo htmlspecialchars($retiro['criptomoneda']); ?></td>
                            <td class="text-center"><?php echo htmlspecialchars($retiro['red']); ?></td>
                            <td class="text-center"><?php echo htmlspecialchars($retiro['wallet']); ?></td>
                            <td class="text-center"><?php echo htmlspecialchars($retiro['fecha_solicitud']); ?></td>
                            <td class="text-center">
                                <div class="d-flex align-items-center gap-10 justify-content-center">
                                    <button type="button" class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#confirmModal" data-id="<?php echo $retiro['id']; ?>" data-action="aprobar">
                                        Aprobar
                                    </button>
                                    <button type="button" class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#confirmModal" data-id="<?php echo $retiro['id']; ?>" data-action="rechazar">
                                        Rechazar
                                    </button>
                                    <a href="ver_retiro.php?id=<?php echo $retiro['id']; ?>" class="btn btn-info btn-sm">Ver</a>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Confirmación -->
<div class="modal fade" id="confirmModal" tabindex="-1" aria-labelledby="confirmModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content radius-16 bg-base">
            <div class="modal-header py-16 px-24 border border-top-0 border-start-0 border-end-0">
                <h1 class="modal-title fs-5" id="confirmModalLabel">Confirmar Acción</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-24">
                <form id="confirmForm" action="procesar_retiro.php" method="POST">
                    <input type="hidden" name="retiro_id" id="retiroId">
                    <input type="hidden" name="action" id="action">
                    <div class="mb-3">
                        <label for="password" class="form-label">Contraseña Administrativa</label>
                        <input type="password" class="form-control" id="password" name="password" required>
                    </div>
                    <div class="d-flex justify-content-end">
                        <button type="button" class="btn btn-secondary me-2" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">Confirmar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('confirmModal').addEventListener('show.bs.modal', function (event) {
    var button = event.relatedTarget;
    var retiroId = button.getAttribute('data-id');
    var action = button.getAttribute('data-action');

    var modalTitle = document.getElementById('confirmModalLabel');
    modalTitle.textContent = action.charAt(0).toUpperCase() + action.slice(1) + ' Retiro';

    var form = document.getElementById('confirmForm');
    document.getElementById('action').value = action;
    document.getElementById('retiroId').value = retiroId;
});
</script>

<?php include './partials/layouts/layoutBottom.php'; ?>
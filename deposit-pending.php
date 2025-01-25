<?php
session_start();
require 'config.php';
require 'functions.php';

// Verificar si el usuario está autenticado
if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit;
}

// Obtener depósitos pendientes
$depositos = getDepositosPendientes($pdo);

// Procesar el formulario de aprobación/rechazo si se envió
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'];
    $action = $_POST['action'];
    $password = $_POST['password'];

    // Verificar la contraseña administrativa
    if ($password === '1BillonUSD') { // Cambia '1BillonUSD' a tu contraseña real
        if ($action === 'aprobar') {
            $resultado = aprobarDeposito($pdo, $id);
            if ($resultado && $resultado['status']) {
                $_SESSION['mensaje'] = "Depósito aprobado y abonado a cliente " . htmlspecialchars($resultado['usuario']) . ".";
            } else {
                $_SESSION['mensaje'] = 'Error al aprobar el depósito: ' . htmlspecialchars($resultado['error']);
            }
        } elseif ($action === 'rechazar') {
            if (rechazarDeposito($pdo, $id)) {
                $_SESSION['mensaje'] = 'Depósito rechazado con éxito.';
            } else {
                $_SESSION['mensaje'] = 'Error al rechazar el depósito.';
            }
        }
    } else {
        $_SESSION['mensaje'] = 'Contraseña incorrecta.';
    }

    header('Location: deposit-pending.php');
    exit;
}

include './partials/layouts/layoutTop.php';
?>

<div class="dashboard-main-body">
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
        <h6 class="fw-semibold mb-0">Depósitos Pendientes</h6>
        <ul class="d-flex align-items-center gap-2">
            <li class="fw-medium">
                <a href="home.php" class="d-flex align-items-center gap-1 hover-text-primary">
                    <iconify-icon icon="solar:home-smile-angle-outline" class="icon text-lg"></iconify-icon>
                    Dashboard
                </a>
            </li>
            <li>-</li>
            <li class="fw-medium">Depósitos Pendientes</li>
        </ul>
    </div>

    <?php if (isset($_SESSION['mensaje'])): ?>
        <div class="alert alert-info">
            <?php echo htmlspecialchars($_SESSION['mensaje']); ?>
            <?php unset($_SESSION['mensaje']); ?>
        </div>
    <?php endif; ?>

    <div class="card h-100 p-0 radius-12">
        <div class="card-body p-24">
            <div class="table-responsive scroll-sm">
                <table class="table bordered-table sm-table mb-0">
                    <thead>
                        <tr>
                            <th scope="col">ID</th>
                            <th scope="col" class="text-center">Usuario ID</th>
                            <th scope="col" class="text-center">Monto</th>
                            <th scope="col" class="text-center">Tipo de Inversión</th>
                            <th scope="col" class="text-center">Fecha</th>
                            <th scope="col" class="text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($depositos as $deposito): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($deposito['id']); ?></td>
                            <td class="text-center"><?php echo htmlspecialchars($deposito['usuario_id']); ?></td>
                            <td class="text-center"><?php echo htmlspecialchars($deposito['monto']); ?></td>
                            <td class="text-center"><?php echo htmlspecialchars($deposito['sistema']); ?></td>
                            <td class="text-center"><?php echo htmlspecialchars($deposito['fecha']); ?></td>
                            <td class="text-center">
                                <div class="d-flex align-items-center gap-10 justify-content-center">
                                    <button type="button" class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#confirmModal" data-id="<?php echo $deposito['id']; ?>" data-action="aprobar">
                                        Aprobar
                                    </button>
                                    <button type="button" class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#confirmModal" data-id="<?php echo $deposito['id']; ?>" data-action="rechazar">
                                        Rechazar
                                    </button>
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
                <form id="confirmForm" method="POST">
                    <input type="hidden" name="id" id="depositoId">
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
    var depositoId = button.getAttribute('data-id');
    var action = button.getAttribute('data-action');

    var modalTitle = document.getElementById('confirmModalLabel');
    modalTitle.textContent = action.charAt(0).toUpperCase() + action.slice(1) + ' Depósito';

    var form = document.getElementById('confirmForm');
    document.getElementById('action').value = action;
    document.getElementById('depositoId').value = depositoId;
});
</script>

<?php include './partials/layouts/layoutBottom.php'; ?>
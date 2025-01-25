<?php
session_start();
require 'config.php';

// Verificar si el usuario está logueado y es administrador
if (!isset($_SESSION['usuario_id']) || $_SESSION['rol'] !== 'administrador') {
    header("Location: login.php");
    exit();
}

// Obtener el capital total, depósitos, retiros, y ganancias de todos los clientes
$sqls = [
    'capital_total' => "SELECT SUM(capital_trading_ia + capital_validator_x) AS capital_total FROM usuarios",
    'depositos_totales' => "SELECT SUM(cantidad) AS depositos_totales FROM historial WHERE tipo_operacion = 'deposito'",
    'retiros_totales' => "SELECT SUM(cantidad) AS retiros_totales FROM historial WHERE tipo_operacion = 'retiro'",
    'ganancias_totales' => "SELECT SUM(ganancias_validator_x + ganancias_trading_ia) AS ganancias_totales FROM usuarios"
];

// Preparar y ejecutar consultas
$results = [];
foreach ($sqls as $key => $sql) {
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $results[$key] = $stmt->fetchColumn();
}

// Obtener detalles de los clientes
$stmtClientes = $pdo->prepare("SELECT id, nombre, capital_trading_ia, capital_validator_x, ganancias_validator_x, ganancias_trading_ia FROM usuarios");
$stmtClientes->execute();
$clientes = $stmtClientes->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pool Total - Administración</title>
    <link rel="stylesheet" href="path/to/styles.css"> <!-- Asegúrate de ajustar la ruta -->
</head>
<body>
<?php include './partials/layouts/layoutTop.php'; ?>
<div class="dashboard-main-body">
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
        <h6 class="fw-semibold mb-0">Pool Total - Administración</h6>
        <ul class="d-flex align-items-center gap-2">
            <li class="fw-medium">
                <a href="index.php" class="d-flex align-items-center gap-1 hover-text-primary">
                    <iconify-icon icon="mdi:home-outline" class="icon text-lg"></iconify-icon>
                    Dashboard
                </a>
            </li>
            <li>-</li>
            <li class="fw-medium">Pool Total</li>
        </ul>
    </div>

    <!-- Tarjetas de Resumen -->
    <div class="row gy-4">
        <!-- Capital Total -->
        <div class="col-xxl-4 col-sm-6">
            <div class="card px-24 py-16 shadow-none radius-8 border h-100 bg-gradient-start-3">
                <div class="card-body p-0">
                    <div class="d-flex align-items-center">
                        <div>
                            <iconify-icon icon="mdi:currency-usd-circle-outline" class="icon text-2xl"></iconify-icon>
                        </div>
                        <div class="ms-3">
                            <span class="mb-2 fw-medium text-secondary-light text-md">Capital Total</span>
                            <h6 class="fw-semibold my-1"><?php echo number_format($results['capital_total'], 2); ?> USDT</h6>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Depósitos Totales -->
        <div class="col-xxl-4 col-sm-6">
            <div class="card px-24 py-16 shadow-none radius-8 border h-100 bg-gradient-start-5">
                <div class="card-body p-0">
                    <div class="d-flex align-items-center">
                        <div>
                            <iconify-icon icon="mdi:cash-plus" class="icon text-2xl"></iconify-icon>
                        </div>
                        <div class="ms-3">
                            <span class="mb-2 fw-medium text-secondary-light text-md">Depósitos Totales</span>
                            <h6 class="fw-semibold my-1"><?php echo number_format($results['depositos_totales'], 2); ?> USDT</h6>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Retiros Totales -->
        <div class="col-xxl-4 col-sm-6">
            <div class="card px-24 py-16 shadow-none radius-8 border h-100 bg-gradient-start-4">
                <div class="card-body p-0">
                    <div class="d-flex align-items-center">
                        <div>
                            <iconify-icon icon="mdi:cash-minus" class="icon text-2xl"></iconify-icon>
                        </div>
                        <div class="ms-3">
                            <span class="mb-2 fw-medium text-secondary-light text-md">Retiros Totales</span>
                            <h6 class="fw-semibold my-1"><?php echo number_format($results['retiros_totales'], 2); ?> USDT</h6>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Ganancias Totales -->
        <div class="col-xxl-4 col-sm-6">
            <div class="card px-24 py-16 shadow-none radius-8 border h-100 bg-gradient-start-3">
                <div class="card-body p-0">
                    <div class="d-flex align-items-center">
                        <div>
                            <iconify-icon icon="mdi:chart-line" class="icon text-2xl"></iconify-icon>
                        </div>
                        <div class="ms-3">
                            <span class="mb-2 fw-medium text-secondary-light text-md">Ganancias Totales</span>
                            <h6 class="fw-semibold my-1"><?php echo number_format($results['ganancias_totales'], 2); ?> USDT</h6>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabla de Clientes -->
    <div class="mt-5">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h6 class="fw-semibold mb-0">Detalle de Clientes</h6>
            <button onclick="downloadCSV()" class="btn btn-primary btn-sm">Descargar CSV</button>
        </div>
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Nombre</th>
                        <th>Capital Trading IA</th>
                        <th>Capital Validator Node</th>
                        <th>Ganancias Trading IA</th>
                        <th>Ganancias Validator Node</th>
                        <th>Capital Total</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($clientes as $cliente): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($cliente['nombre']); ?></td>
                            <td><?php echo number_format($cliente['capital_trading_ia'], 2); ?> USDT</td>
                            <td><?php echo number_format($cliente['capital_validator_x'], 2); ?> USDT</td>
                            <td><?php echo number_format($cliente['ganancias_trading_ia'], 2); ?> USDT</td>
                            <td><?php echo number_format($cliente['ganancias_validator_x'], 2); ?> USDT</td>
                            <td><?php echo number_format($cliente['capital_trading_ia'] + $cliente['capital_validator_x'], 2); ?> USDT</td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<style>
    .table-responsive {
        overflow-x: auto;
    }
    .card {
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }
    .bg-gradient-start-3 {
        background: linear-gradient(135deg, #ffffff, #f0f0f0);
    }
    .bg-gradient-start-5 {
        background: linear-gradient(135deg, #ffffff, #e0e0e0);
    }
    .bg-gradient-start-4 {
        background: linear-gradient(135deg, #ffffff, #d0d0d0);
    }
    .icon {
        font-size: 1.5rem;
    }
</style>

<script>
function downloadCSV() {
    var csv = [];
    var rows = document.querySelectorAll("table tr");

    for (var i = 0; i < rows.length; i++) {
        var row = [], cols = rows[i].querySelectorAll("td, th");

        for (var j = 0; j < cols.length; j++) 
            row.push(cols[j].innerText);

        csv.push(row.join(","));        
    }

    var csvString = csv.join("\n");
    var link = document.createElement("a");
    link.href = 'data:text/csv;charset=utf-8,' + encodeURIComponent(csvString);
    link.target = '_blank';
    link.download = 'clientes.csv';
    link.click();
}
</script>

<?php include './partials/layouts/layoutBottom.php'; ?>
</body>
</html>
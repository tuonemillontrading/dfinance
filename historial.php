<?php include './partials/layouts/layoutTop.php'; ?>
<link rel="stylesheet" href="path/to/styles.css"> <!-- Asegúrate de ajustar la ruta -->
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

// Obtener el historial de depósitos y retiros del usuario
$query = "
    SELECT id, user_id, 'deposito' as tipo_operacion, sistema, cantidad, fecha, NULL as criptomoneda, NULL as red, NULL as wallet, NULL as estado
    FROM historial 
    WHERE user_id = :user_id AND tipo_operacion = 'deposito'
    UNION ALL
    SELECT id, usuario_id as user_id, 'retiro' as tipo_operacion, NULL as sistema, cantidad, fecha_solicitud as fecha, criptomoneda, red, wallet, estado
    FROM retiros 
    WHERE usuario_id = :user_id 
    ORDER BY fecha DESC";
$stmt = $pdo->prepare($query);
$stmt->execute(['user_id' => $userId]);
$historial = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="dashboard-main-body">
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
        <h6 class="fw-semibold mb-0">Historial de Transacciones</h6>
        <ul class="d-flex align-items-center gap-2">
            <li class="fw-medium">
                <a href="index.php" class="d-flex align-items-center gap-1 hover-text-primary">
                    <iconify-icon icon="solar:home-smile-angle-outline" class="icon text-lg"></iconify-icon>
                    Dashboard
                </a>
            </li>
            <li>-</li>
            <li class="fw-medium">Historial de Transacciones</li>
        </ul>
    </div>

    <!-- Botones de Descarga CSV e Imprimir -->
    <div class="d-flex justify-content-end mb-3">
        <button onclick="downloadCSV()" class="btn btn-primary btn-sm me-2">Descargar CSV</button>
        <button onclick="window.print()" class="btn btn-secondary btn-sm">Imprimir</button>
    </div>

    <div class="mb-24">
        <h2></h2>
        <h3></h3>
        
        <label for="buscar">Buscar:</label>
        <input type="text" id="buscar" onkeyup="filtrarTabla()" placeholder="Buscar por fecha, monto o plan..." class="form-control bg-transparent text-white">

        <table id="historialTabla" class="table table-striped mt-3" style="color: #273142;">
            <thead>
                <tr style="font-size: 12px; background-color: transparent;">
                    <th>Fecha</th>
                    <th>Monto</th>
                    <th>Tipo</th>
                    <th>Plan/Depósito</th>
                    <th>Detalles</th>
                    <th>Estado</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($historial) > 0): ?>
                    <?php foreach ($historial as $transaccion): ?>
                        <tr style="font-size: 12px; background-color: transparent;">
                            <td><?php echo htmlspecialchars($transaccion['fecha']); ?></td>
                            <td <?php echo $transaccion['tipo_operacion'] == 'retiro' ? 'style="color: red;"' : ''; ?>>
                                <?php 
                                $monto = $transaccion['cantidad'];
                                if ($transaccion['tipo_operacion'] == 'retiro') {
                                    $monto = -$monto;
                                }
                                echo htmlspecialchars(number_format($monto, 2)); 
                                ?> USDT
                            </td>
                            <td><?php echo htmlspecialchars($transaccion['tipo_operacion'] == 'deposito' ? 'Depósito' : 'Retiro'); ?></td>
                            <td><?php echo htmlspecialchars($transaccion['sistema'] ?? '-'); ?></td>
                            <td>
                                <?php 
                                if ($transaccion['tipo_operacion'] == 'retiro') {
                                    echo 'Criptomoneda: ' . htmlspecialchars($transaccion['criptomoneda']) . '<br>';
                                    echo 'Red: ' . htmlspecialchars($transaccion['red']) . '<br>';
                                    echo 'Wallet: ' . htmlspecialchars($transaccion['wallet']);
                                } else {
                                    echo '-';
                                }
                                ?>
                            </td>
                            <td>
                                <?php 
                                if ($transaccion['tipo_operacion'] == 'retiro') {
                                    echo htmlspecialchars($transaccion['estado']);
                                } else {
                                    echo '-';
                                }
                                ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6" class="text-center">No hay datos disponibles en la tabla</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
function filtrarTabla() {
    var input, filter, table, tr, td, i, txtValue;
    input = document.getElementById("buscar");
    filter = input.value.toUpperCase();
    table = document.getElementById("historialTabla");
    tr = table.getElementsByTagName("tr");

    for (i = 1; i < tr.length; i++) {
        tr[i].style.display = "none";
        td = tr[i].getElementsByTagName("td");
        for (var j = 0; j < td.length; j++) {
            if (td[j]) {
                txtValue = td[j].textContent || td[j].innerText;
                if (txtValue.toUpperCase().indexOf(filter) > -1) {
                    tr[i].style.display = "";
                    break;
                }
            }
        }
    }
}

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
    link.download = 'historial_transacciones.csv';
    link.click();
}
</script>

<?php include './partials/layouts/layoutBottom.php'; ?>
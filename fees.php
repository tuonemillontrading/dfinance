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

// Obtener el historial de ganancias de staking del usuario
$query = "
    SELECT id, user_id, tipo_operacion, sistema, cantidad, fecha
    FROM historial 
    WHERE user_id = :user_id AND tipo_operacion = 'ganancia'
    ORDER BY fecha DESC";
$stmt = $pdo->prepare($query);
$stmt->execute(['user_id' => $userId]);
$historial = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="dashboard-main-body">
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
        <h6 class="fw-semibold mb-0">Historial</h6>
        <ul class="d-flex align-items-center gap-2">
            <li class="fw-medium">
                <a href="index.php" class="d-flex align-items-center gap-1 hover-text-primary">
                    <iconify-icon icon="solar:home-smile-angle-outline" class="icon text-lg"></iconify-icon>
                    Dashboard
                </a>
            </li>
            <li>-</li>
            <li class="fw-medium">Historial de Ganancias de Staking</li>
        </ul>
    </div>

    <div class="mb-24">
        <h2></h2>
        <h3></h3>

        <div class="d-flex justify-content-between mb-3">
            <button onclick="downloadCSV()" class="btn btn-primary btn-sm">Descargar CSV</button>
            <button onclick="window.print()" class="btn btn-secondary btn-sm">Imprimir</button>
        </div>

        <table id="historialTabla" class="table table-striped mt-3" style="color: #273142;">
            <thead>
                <tr style="font-size: 12px; background-color: transparent;">
                    <th>Fecha</th>
                    <th>Monto</th>
                    <th>Tipo</th>
                    <th>    Plan</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($historial) > 0): ?>
                    <?php foreach ($historial as $transaccion): ?>
                        <tr style="font-size: 12px; background-color: transparent;">
                            <td><?php echo htmlspecialchars($transaccion['fecha']); ?></td>
                            <td><?php echo htmlspecialchars(number_format($transaccion['cantidad'], 2)); ?> USDT</td>
                            <td><?php echo htmlspecialchars($transaccion['tipo_operacion'] == 'ganancia' ? 'Fee' : $transaccion['tipo_operacion']); ?></td>
                            <td><?php echo htmlspecialchars($transaccion['sistema'] ?? '-'); ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="4" class="text-center">No hay datos disponibles en la tabla</td>
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
    link.download = 'historial_ganancias_staking.csv';
    link.click();
}
</script>

<?php include './partials/layouts/layoutBottom.php'; ?>
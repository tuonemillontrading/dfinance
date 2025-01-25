<?php
session_start();
require 'config.php';

// Verificar si el usuario está logueado
if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit();
}

// Obtener el ID del usuario desde la sesión
$usuario_id = $_SESSION['usuario_id'];

// Consultas a la base de datos
$sqls = [
    'nombre_usuario' => "SELECT nombre FROM usuarios WHERE id = :usuario_id",
    'ganancias_totales' => "SELECT SUM(ganancias_validator_x + ganancias_trading_ia) AS ganancias_totales FROM usuarios WHERE id = :usuario_id",
    'ganancias_trading_ia' => "SELECT ganancias_trading_ia FROM usuarios WHERE id = :usuario_id",
    'ganancias_validator_x' => "SELECT ganancias_validator_x FROM usuarios WHERE id = :usuario_id",
    'capital_trading_ia' => "SELECT capital_trading_ia FROM usuarios WHERE id = :usuario_id",
    'capital_validator_x' => "SELECT capital_validator_x FROM usuarios WHERE id = :usuario_id",
    'ganancias_hoy_trading_ia' => "SELECT SUM(cantidad) AS ganancias_hoy FROM historial WHERE user_id = :usuario_id AND sistema = 'trading_ia' AND tipo_operacion = 'ganancia' AND DATE(fecha) = CURDATE()",
    'ganancias_hoy_validator' => "SELECT SUM(cantidad) AS ganancias_hoy FROM historial WHERE user_id = :usuario_id AND sistema = 'validator_node' AND tipo_operacion = 'ganancia' AND DATE(fecha) = CURDATE()",
    'ganancias_esta_semana' => "SELECT SUM(cantidad) AS ganancias_esta_semana FROM historial WHERE user_id = :usuario_id AND sistema IN ('trading_ia', 'validator_node') AND tipo_operacion = 'ganancia' AND fecha >= CURDATE() - INTERVAL 7 DAY",
    'ganancias_semana_pasada' => "SELECT SUM(cantidad) AS ganancias_semana_pasada FROM historial WHERE user_id = :usuario_id AND sistema IN ('trading_ia', 'validator_node') AND tipo_operacion = 'ganancia' AND fecha >= CURDATE() - INTERVAL 14 DAY AND fecha < CURDATE() - INTERVAL 7 DAY",
    'ganancias_mensuales_trading_ia' => "SELECT DATE_FORMAT(fecha, '%Y-%m') AS mes, SUM(cantidad) AS ganancias FROM historial WHERE user_id = :usuario_id AND sistema = 'trading_ia' AND tipo_operacion = 'ganancia' GROUP BY DATE_FORMAT(fecha, '%Y-%m') ORDER BY fecha ASC",
    'ganancias_mensuales_validator' => "SELECT DATE_FORMAT(fecha, '%Y-%m') AS mes, SUM(cantidad) AS ganancias FROM historial WHERE user_id = :usuario_id AND sistema = 'validator_node' AND tipo_operacion = 'ganancia' GROUP BY DATE_FORMAT(fecha, '%Y-%m') ORDER BY fecha ASC"
];

// Prepare and execute queries
$results = [];
foreach ($sqls as $key => $sql) {
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['usuario_id' => $usuario_id]);
    $results[$key] = $stmt->fetchColumn() ?: 0;  // Ensure a 0 value if null
}

// Calcular las ganancias totales de hoy
$ganancias_hoy = $results['ganancias_hoy_trading_ia'] + $results['ganancias_hoy_validator'];

// Calcular el capital total invertido
$capital_total_invertido = $results['capital_trading_ia'] + $results['capital_validator_x'];

// Calcular el porcentaje de aumento semanal
$ganancias_semana_pasada = $results['ganancias_semana_pasada'];
$ganancias_esta_semana = $results['ganancias_esta_semana'];
$porcentaje_aumento = ($ganancias_semana_pasada > 0) ? (($ganancias_esta_semana - $ganancias_semana_pasada) / $ganancias_semana_pasada) * 100 : 100;

// Consultar rendimientos mensuales de Trading IA
$stmt = $pdo->prepare($sqls['ganancias_mensuales_trading_ia']);
$stmt->execute(['usuario_id' => $usuario_id]);
$rendimientos_mensuales_trading_ia = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Consultar rendimientos mensuales de Validator Node
$stmt = $pdo->prepare($sqls['ganancias_mensuales_validator']);
$stmt->execute(['usuario_id' => $usuario_id]);
$rendimientos_mensuales_validator = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Convertir los datos mensuales a formato JSON
$labels = [];
$data_trading_ia = [];
$data_validator_node = [];
$months = [
    '01' => 'Enero',
    '02' => 'Febrero',
    '03' => 'Marzo',
    '04' => 'Abril',
    '05' => 'Mayo',
    '06' => 'Junio',
    '07' => 'Julio',
    '08' => 'Agosto',
    '09' => 'Septiembre',
    '10' => 'Octubre',
    '11' => 'Noviembre',
    '12' => 'Diciembre',
];

foreach ($rendimientos_mensuales_trading_ia as $row) {
    $dateParts = explode('-', $row['mes']);
    $monthName = $months[$dateParts[1]];
    $labels[] = $monthName . ' ' . $dateParts[0];
    $data_trading_ia[] = $row['ganancias'];
}

foreach ($rendimientos_mensuales_validator as $row) {
    $data_validator_node[] = $row['ganancias'];
}
?>

<!DOCTYPE html>
<html lang="en" data-theme="light">
<?php include './partials/head.php' ?>
<body>
<?php include './partials/layouts/layoutTop.php' ?>

<div class="dashboard-main-body">
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
        <h6 class="fw-semibold mb-0"></h6>
        <ul class="d-flex align-items-center gap-2">
            <li class="fw-medium">
                <a href="index.php" class="d-flex align-items-center gap-1 hover-text-primary"></a>
            </li>
            <li class="fw-medium"></li>
        </ul>
    </div>

    <!-- Saludo personalizado -->
    <div class="mb-24">
        <h6 class="fw-semibold mb-0">👋Hola de nuevo, <?php echo htmlspecialchars($results['nombre_usuario']); ?></h6>
    </div>

    <div class="row gy-4">
        <!-- Dashboard Widget -->
        <div class="col-xxl-4 col-sm-6">
            <div class="card px-24 py-16 shadow-none radius-8 border h-100 bg-gradient-start-3">
                <div class="card-body p-0">
                    <div class="d-flex flex-wrap align-items-center justify-content-between gap-1 mb-8">
                        <div class="d-flex align-items-center">
                            <div>
                                <iconify-icon icon="mdi:USD" class="icon text-2xl"></iconify-icon>
                            </div>
                            <div class="ms-3">
                                <span class="mb-2 fw-medium text-secondary-light text-md">💰Ganancias Totales</span>
                                <h6 class="fw-semibold my-1"><?php echo number_format($results['ganancias_totales'], 2); ?> USDT</h6>
                                <p class="text-sm mb-0">
                                    <span class="bg-success-focus px-1 rounded-2 fw-medium text-success-main text-sm">
                                        +<?php echo ($capital_total_invertido > 0) ? number_format(($results['ganancias_totales'] / $capital_total_invertido) * 100, 2) : 0; ?>%
                                    </span>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Trading IA Widget -->
        <div class="col-xxl-4 col-sm-6">
            <div class="card px-24 py-16 shadow-none radius-8 border h-100 bg-gradient-start-5">
                <div class="card-body p-0">
                    <div class="d-flex flex-wrap align-items-center justify-content-between gap-1 mb-8">
                        <div class="d-flex align-items-center">
                            <div class="w-72-px h-72-px radius-16 bg-blue d-flex justify-content-center align-items-center me-20">
                                <span class="mb-0 w-48-px h-48-px bg-blue flex-shrink-0 text-white d-flex justify-content-center align-items-center radius-8 h6 mb-0">
                                    <iconify-icon icon="mdi:robot-outline" class="text-white text-2xl mb-0"></iconify-icon>
                                </span>
                            </div>
                            <div>
                                <span class="mb-2 fw-medium text-secondary-light text-md">Trading IA</span>
                                <h6 class="fw-semibold my-1"><?php echo number_format($results['ganancias_trading_ia'], 2); ?> USDT</h6>
                                <p class="text-sm mb-0">
                                    <span class="bg-success-focus px-1 rounded-2 fw-medium text-success-main text-sm">
                                        +<?php echo ($results['capital_trading_ia'] > 0) ? number_format(($results['ganancias_trading_ia'] / $results['capital_trading_ia']) * 100, 2) : 0; ?>%
                                    </span>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Validator Node Widget -->
        <div class="col-xxl-4 col-sm-6">
            <div class="card px-24 py-16 shadow-none radius-8 border h-100 bg-gradient-start-4">
                <div class="card-body p-0">
                    <div class="d-flex flex-wrap align-items-center justify-content-between gap-1 mb-8">
                        <div class="d-flex align-items-center">
                            <div class="w-72-px h-72-px radius-16 bg-green d-flex justify-content-center align-items-center me-20">
                                <span class="mb-0 w-48-px h-48-px bg-green flex-shrink-0 text-white d-flex justify-content-center align-items-center radius-8 h6 mb-0">
                                    <iconify-icon icon="mdi:shield-check-outline" class="text-white text-2xl mb-0"></iconify-icon>
                                </span>
                            </div>
                            <div>
                                <span class="mb-2 fw-medium text-secondary-light text-md">Validator Node</span>
                                <h6 class="fw-semibold my-1"><?php echo number_format($results['ganancias_validator_x'], 2); ?> USDT</h6>
                                <p class="text-sm mb-0">
                                    <span class="bg-success-focus px-1 rounded-2 fw-medium text-success-main text-sm">
                                        +<?php echo ($results['capital_validator_x'] > 0) ? number_format(($results['ganancias_validator_x'] / $results['capital_validator_x']) * 100, 2) : 0; ?>%
                                    </span>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Revenue Statistics and Capital Cards -->
        <div class="row gy-4 mt-1">
<!-- Revenue Statistics and Chart -->
<div class="col-xxl-8 col-xl-12">
    <div class="card h-100">
        <div class="card-body">
            <div class="d-flex flex-wrap align-items-center justify-content-between">
                <h6 class="text-lg mb-0">La evolución de tu capital</h6>
            </div>
            <div class="d-flex flex-wrap align-items-center justify-content-center mt-4">
                <span id="showTradingIA" class="text-primary me-3" style="cursor: pointer;">Trading IA</span>
                <span id="showValidatorNode" class="text-secondary" style="cursor: pointer;">Validator Node</span>
            </div>
            <div class="pt-28 apexcharts-tooltip-style-1">
                <canvas id="gananciasChart"></canvas>
            </div>
        </div>
    </div>
</div>

            <!-- Capital Total -->
            <div class="col-xxl-4 col-xl-12">
                <div class="card px-24 py-16 shadow-none radius-8 border h-100">
                    <div class="card-body p-0">
                        <h6 class="fw-semibold text-lg mb-3" style="margin-top: 10px;">Capital Total</h6>
                        <!-- Capital Validator Node -->
                        <div class="d-flex flex-wrap align-items-center justify-content-between gap-1 mb-8">
                            <div class="d-flex align-items-center me-20">
                                <div>
                                    <h6 class="fw-semibold my-1"><?php echo number_format($results['capital_validator_x'], 2); ?> USDT</h6>
                                    <span class="mb-2 fw-medium text-secondary-light text-md">Validator Node</span>
                                </div>
                            </div>
                        </div>
                        <!-- Capital Trading IA -->
                        <div class="d-flex flex-wrap align-items-center justify-content-between gap-1 mt-4">
                            <div class="d-flex align-items-center me-20">
                                <div>
                                    <h6 class="fw-semibold my-1"><?php echo number_format($results['capital_trading_ia'], 2); ?> USDT</h6>
                                    <span class="mb-2 fw-medium text-secondary-light text-md">Trading IA</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<style>
    .card {
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }
    .chartjs-render-monitor {
        background-color: #1B2431;
    }
    .bg-gradient-start-3 {
        background: linear-gradient(135deg, #1B2431, #273142);
    }
    .bg-gradient-start-5 {
        background: linear-gradient(135deg, #1468E7, #1B2431);
    }
    .bg-gradient-start-4 {
        background: linear-gradient(135deg, #28a745, #1B2431);
    }
</style>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Pasar los datos a JavaScript
    var labels = <?php echo json_encode($labels); ?>;
    var data_trading_ia = <?php echo json_encode($data_trading_ia); ?>;
    var data_validator_node = <?php echo json_encode($data_validator_node); ?>;

    // Verificar los datos en la consola
    console.log("Labels:", labels);
    console.log("Data Trading IA:", data_trading_ia);
    console.log("Data Validator Node:", data_validator_node);

    // Configuración del gráfico
    var ctx = document.getElementById('gananciasChart').getContext('2d');
    var gananciasChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [{
                label: 'Ganancias Trading IA',
                data: data_trading_ia,
                borderColor: '#1468E7',
                borderWidth: 3,
                fill: true,
                backgroundColor: 'rgba(20, 104, 231, 0.2)',
                pointBackgroundColor: '#fff',
                pointBorderColor: '#fff',
                pointHoverBackgroundColor: '#fff',
                pointHoverBorderColor: '#fff',
                lineTension: 0
            }]
        },
        options: {
            responsive: true,
            scales: {
                x: {
                    type: 'category',
                    title: {
                        display: false,
                        color: '#fff',
                        font: {
                            size: 14
                        }
                    },
                    ticks: {
                        color: '#fff',
                        font: {
                            size: 14
                        }
                    },
                    grid: {
                        color: 'rgba(255, 255, 255, 0.1)'
                    }
                },
                y: {
                    beginAtZero: true,
                    title: {
                        display: false,
                        color: '#fff',
                        font: {
                            size: 14
                        }
                    },
                    ticks: {
                        color: '#fff',
                        font: {
                            size: 14
                        }
                    },
                    grid: {
                        color: 'rgba(255, 255, 255, 0.1)'
                    }
                }
            },
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            let label = context.dataset.label || '';
                            if (label) {
                                label += ': ';
                            }
                            label += context.parsed.y + ' USDT';
                            return label;
                        },
                        title: function(context) {
                            return context[0].label;
                        }
                    },
                    backgroundColor: 'rgba(0, 0, 0, 0.7)',
                    titleColor: '#fff',
                    titleFont: {
                        size: 14
                    },
                    bodyColor: '#fff',
                    bodyFont: {
                        size: 14
                    },
                    displayColors: false
                }
            }
        }
    });

    // Alternar entre Trading IA y Validator Node
    document.getElementById('showTradingIA').addEventListener('click', function() {
        gananciasChart.data.datasets[0].data = data_trading_ia;
        gananciasChart.data.datasets[0].label = 'Ganancias Trading IA';
        document.getElementById('showTradingIA').classList.add('text-primary');
        document.getElementById('showValidatorNode').classList.remove('text-primary');
        document.getElementById('showValidatorNode').classList.add('text-secondary');
        gananciasChart.update();
    });

    document.getElementById('showValidatorNode').addEventListener('click', function() {
        gananciasChart.data.datasets[0].data = data_validator_node;
        gananciasChart.data.datasets[0].label = 'Ganancias Validator Node';
        document.getElementById('showValidatorNode').classList.add('text-primary');
        document.getElementById('showTradingIA').classList.remove('text-primary');
        document.getElementById('showTradingIA').classList.add('text-secondary');
        gananciasChart.update();
    });
</script>

<?php include './partials/layouts/layoutBottom.php' ?>
</body>
</html>
<?php
require 'config.php';
require 'middleware.php';

check_auth();

$usuario_id = $_SESSION['usuario_id'];
$rol = $_SESSION['rol'];

// Obtener información específica del usuario
$sql = "SELECT * FROM usuarios WHERE id = :id";
$stmt = $pdo->prepare($sql);
$stmt->execute(['id' => $usuario_id]);
$usuario = $stmt->fetch();
?>

<?php $script = '<script src="assets/js/homeFiveChart.js"></script><script src="assets/js/homeOneChart.js"></script>';?>

<?php include './partials/layouts/layoutTop.php' ?>

        <div class="dashboard-main-body">

            <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
                <h6 class="fw-semibold mb-0">Dashboard</h6>
                <ul class="d-flex align-items-center gap-2">
                    <li class="fw-medium">
                        <a href="index.php" class="d-flex align-items-center gap-1 hover-text-primary">
                            <iconify-icon icon="solar:home-smile-angle-outline" class="icon text-lg"></iconify-icon>
                            Dashboard
                        </a>
                    </li>
                    <li>-</li>
                    <li class="fw-medium">Investment</li>
                </ul>
            </div>

            <div class="row gy-4">

                <!-- Dashboard Widget Start -->
                <div class="col-xxl-3 col-sm-6">
                    <div class="card px-24 py-16 shadow-none radius-8 border h-100 bg-gradient-start-3">
                        <div class="card-body p-0">
                            <div class="d-flex flex-wrap align-items-center justify-content-between gap-1 mb-8">
                                <div class="d-flex align-items-center">

                                    <div class="w-64-px h-64-px radius-16 bg-base-50 d-flex justify-content-center align-items-center me-20">
                                        <span class="mb-0 w-40-px h-40-px bg-primary-600 flex-shrink-0 text-white d-flex justify-content-center align-items-center radius-8 h6 mb-0">
                                            <iconify-icon icon="flowbite:users-group-solid" class="icon"></iconify-icon>
                                        </span>
                                    </div>

                                    <div>
                                        <span class="mb-2 fw-medium text-secondary-light text-md">Tus Ganancias Totales</span>
                                        <h6 class="fw-semibold my-1">5000</h6>
                                        <p class="text-sm mb-0">Increase by
                                            <span class="bg-success-focus px-1 rounded-2 fw-medium text-success-main text-sm">+200</span> this week
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Dashboard Widget End -->


                <!-- Dashboard Widget Start -->
                <div class="col-xxl-3 col-sm-6">
                    <div class="card px-24 py-16 shadow-none radius-8 border h-100 bg-gradient-start-5">
                        <div class="card-body p-0">
                            <div class="d-flex flex-wrap align-items-center justify-content-between gap-1 mb-8">
                                <div class="d-flex align-items-center">

                                    <div class="w-64-px h-64-px radius-16 bg-base-50 d-flex justify-content-center align-items-center me-20">
                                        <span class="mb-0 w-40-px h-40-px bg-red flex-shrink-0 text-white d-flex justify-content-center align-items-center radius-8 h6 mb-0">
                                            <iconify-icon icon="fa6-solid:file-invoice-dollar" class="text-white text-2xl mb-0"></iconify-icon>
                                        </span>
                                    </div>

                                    <div>
                                        <span class="mb-2 fw-medium text-secondary-light text-md">Trading IA</span>
                                        <h6 class="fw-semibold my-1">15,000</h6>
                                        <p class="text-sm mb-0">Increase by
                                            <span class="bg-success-focus px-1 rounded-2 fw-medium text-success-main text-sm">+200</span> this week
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Dashboard Widget End -->

                <!-- Dashboard Widget Start -->
                <div class="col-xxl-3 col-sm-6">
                    <div class="card px-24 py-16 shadow-none radius-8 border h-100 bg-gradient-start-4">
                        <div class="card-body p-0">
                            <div class="d-flex flex-wrap align-items-center justify-content-between gap-1 mb-8">
                                <div class="d-flex align-items-center">

                                    <div class="w-64-px h-64-px radius-16 bg-base-50 d-flex justify-content-center align-items-center me-20">
                                        <span class="mb-0 w-40-px h-40-px bg-success-main flex-shrink-0 text-white d-flex justify-content-center align-items-center radius-8 h6 mb-0">
                                            <iconify-icon icon="streamline:bag-dollar-solid" class="icon"></iconify-icon>
                                        </span>
                                    </div>

                                    <div>
                                        <span class="mb-2 fw-medium text-secondary-light text-md">Profits Today</span>
                                        <h6 class="fw-semibold my-1">15,000</h6>
                                        <p class="text-sm mb-0">Increase by
                                            <span class="bg-success-focus px-1 rounded-2 fw-medium text-success-main text-sm">+200</span> this week
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Dashboard Widget End -->

                <!-- Revenue Statistics Start -->
                <div class="row gy-4 mt-1">
                <div class="col-xxl-6 col-xl-12">
                    <div class="card h-100">
                        <div class="card-body">
                            <div class="d-flex flex-wrap align-items-center justify-content-between">
                                <h6 class="text-lg mb-0">Sales Statistic</h6>
                                <select class="form-select bg-base form-select-sm w-auto">
                                    <option>Yearly</option>
                                    <option>Monthly</option>
                                    <option>Weekly</option>
                                    <option>Today</option>
                                </select>
                            </div>
                            <div class="d-flex flex-wrap align-items-center gap-2 mt-8">
                                <h6 class="mb-0">$27,200</h6>
                                <span class="text-sm fw-semibold rounded-pill bg-success-focus text-success-main border br-success px-8 py-4 line-height-1 d-flex align-items-center gap-1">
                                    10% <iconify-icon icon="bxs:up-arrow" class="text-xs"></iconify-icon>
                                </span>
                                <span class="text-xs fw-medium">+ $1500 Per Day</span>
                            </div>
                            <div id="chart" class="pt-28 apexcharts-tooltip-style-1"></div>
                        </div>
                    </div>
                </div>
                <!-- Revenue Statistics End -->

                <!-- Statistics Start -->
                <div class="col-xxl-4">
                    <div class="card h-100 radius-8 border-0">
                        <div class="card-body p-24">
                            <h6 class="mb-2 fw-bold text-lg">Statistic</h6>

                            <div class="mt-24">
                                <div class="d-flex align-items-center gap-1 justify-content-between mb-44">
                                    <div>
                                        <span class="text-secondary-light fw-normal mb-12 text-xl">Daily Conversions</span>
                                        <h5 class="fw-semibold mb-0">%60</h5>
                                    </div>
                                    <div class="position-relative">
                                        <div id="semiCircleGauge"></div>

                                        <span class="w-36-px h-36-px rounded-circle bg-neutral-100 d-flex justify-content-center align-items-center position-absolute start-50 translate-middle top-100">
                                            <iconify-icon icon="mdi:emoji" class="text-primary-600 text-md mb-0"></iconify-icon>
                                        </span>
                                    </div>
                                </div>

                                <div class="d-flex align-items-center gap-1 justify-content-between mb-44">
                                    <div>
                                        <span class="text-secondary-light fw-normal mb-12 text-xl">Visits By Day</span>
                                        <h5 class="fw-semibold mb-0">20k</h5>
                                    </div>
                                    <div id="areaChart"></div>
                                </div>

                                <div class="d-flex align-items-center gap-1 justify-content-between">
                                    <div>
                                        <span class="text-secondary-light fw-normal mb-12 text-xl">Today Income</span>
                                        <h5 class="fw-semibold mb-0">$5.5k</h5>
                                    </div>
                                    <div id="dailyIconBarChart"></div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
                <!-- Statistics End -->

             

                <!-- Total Transactions Start -->
                <div class="col-xxl-4">
                    <div class="card h-100">
                        <div class="card-body p-24">
                            <div class="d-flex align-items-center flex-wrap gap-2 justify-content-between mb-20">
                                <h6 class="mb-2 fw-bold text-lg">Total Transactions </h6>
                                <div class="">
                                    <select class="form-select form-select-sm w-auto bg-base border text-secondary-light">
                                        <option>Yearly</option>
                                        <option>Monthly</option>
                                        <option>Weekly</option>
                                        <option>Today</option>
                                    </select>
                                </div>
                            </div>

                            <ul class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-28">
                                <li class="d-flex align-items-center gap-2">
                                    <span class="w-16-px h-16-px radius-2 bg-primary-600"></span>
                                    <span class="text-secondary-light text-lg fw-normal">Total Gain:
                                        <span class="text-primary-light fw-bold text-lg">$50,000</span>
                                    </span>
                                </li>
                            </ul>

                            <div id="transactionLineChart"></div>

                        </div>
                    </div>
                </div>
                <!-- Total Transactions End -->

                

            </div>

        </div>
        </div>
                    </div>
                </div>
<?php include './partials/layouts/layoutBottom.php' ?>
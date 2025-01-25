<?php
include './partials/layouts/layoutTop.php';

// Incluir el archivo de configuración
$config_file = 'config.php';
if (file_exists($config_file)) {
    include $config_file;
} else {
    die("Error: No se pudo encontrar el archivo de configuración de la base de datos.");
}

// Verificar si la conexión a la base de datos está configurada
if (!isset($pdo)) {
    die("Error: No se pudo establecer la conexión a la base de datos.");
}

// Consulta para obtener el histórico de depósitos
$sql = "SELECT d.id, u.nombre AS usuario, d.monto, d.estado, d.fecha, d.sistema 
        FROM depositos d
        JOIN usuarios u ON d.usuario_id = u.id 
        ORDER BY d.fecha DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute();
$result = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<style>
    /* Estilos generales para la tabla */
    .table-transparente {
        background-color: transparent;
        width: 100%;
        border-collapse: collapse;
    }
    .table-transparente th, .table-transparente td {
        border: 1px solid #dee2e6;
        padding: 8px;
        text-align: left;
    }
    
    /* Estilos para el tema claro */
    body[data-theme='light'] .table-transparente th, 
    body[data-theme='light'] .table-transparente td {
        color: #000000; /* Texto negro para tema claro */
        border-color: #dee2e6; /* Color del borde para tema claro */
    }
    
    /* Estilos para el tema oscuro */
    body[data-theme='dark'] .table-transparente th, 
    body[data-theme='dark'] .table-transparente td {
        color: #ffffff; /* Texto blanco para tema oscuro */
        border-color: #444; /* Color del borde para tema oscuro */
        background-color: #273142; /* Fondo oscuro personalizado */
    }
</style>

<div class="dashboard-main-body">
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
        <h6 class="fw-semibold mb-0">Histórico de Depósitos</h6>
        <ul class="d-flex align-items-center gap-2">
            <li class="fw-medium">
                <a href="index.php" class="d-flex align-items-center gap-1 hover-text-primary">
                    <iconify-icon icon="solar:home-smile-angle-outline" class="icon text-lg"></iconify-icon>
                    Dashboard
                </a>
            </li>
            <li>-</li>
            <li class="fw-medium">Histórico de Depósitos</li>
        </ul>
    </div>

    <table class="table table-transparente">
        <thead>
            <tr>
                <th>ID</th>
                <th>Usuario</th>
                <th>Monto</th>
                <th>Estado</th>
                <th>Fecha</th>
                <th>Sistema</th>
            </tr>
        </thead>
        <tbody>
            <?php
            if (count($result) > 0) {
                // Mostrar resultados de cada fila
                foreach ($result as $row) {
                    echo "<tr>
                            <td>" . htmlspecialchars($row["id"]) . "</td>
                            <td>" . htmlspecialchars($row["usuario"]) . "</td>
                            <td>" . htmlspecialchars($row["monto"]) . "</td>
                            <td>" . htmlspecialchars($row["estado"]) . "</td>
                            <td>" . htmlspecialchars($row["fecha"]) . "</td>
                            <td>" . htmlspecialchars($row["sistema"]) . "</td>
                          </tr>";
                }
            } else {
                echo "<tr><td colspan='6'>No hay depósitos</td></tr>";
            }
            ?>
        </tbody>
    </table>
</div>

<?php
include './partials/layouts/layoutBottom.php';
?>
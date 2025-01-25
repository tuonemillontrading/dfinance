<?php
include 'config.php';  // Incluir el archivo de configuración de la base de datos
include 'auth.php';  // Incluir el archivo de autenticación

// Verificar si el usuario está autenticado y es administrador
check_admin();

$mensaje = '';

// Verificar si se ha proporcionado un ID de usuario
if (!isset($_GET['id'])) {
    die("User ID is required");
}

$userId = $_GET['id'];

// Manejar el envío del formulario
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = $_POST['id'];
    $nombre = $_POST['nombre'];
    $correo = $_POST['correo'];
    $capital_validator_x = $_POST['capital_validator_x'];
    $capital_trading_ia = $_POST['capital_trading_ia'];
    $rol = $_POST['rol'];

    // Validar que los campos requeridos no estén vacíos
    if (!empty($nombre) && !empty($correo)) {
        try {
            $stmt = $pdo->prepare("UPDATE usuarios SET nombre = :nombre, correo = :correo, capital_validator_x = :capital_validator_x, capital_trading_ia = :capital_trading_ia, rol = :rol WHERE id = :id");
            $stmt->execute([
                ':id' => $id,
                ':nombre' => $nombre,
                ':correo' => $correo,
                ':capital_validator_x' => $capital_validator_x,
                ':capital_trading_ia' => $capital_trading_ia,
                ':rol' => $rol,
            ]);
            $mensaje = "<div style='color: green; font-weight: bold; margin-top: 20px;'>Usuario actualizado exitosamente.</div>";
        } catch (PDOException $e) {
            $mensaje = "<div style='color: red; font-weight: bold; margin-top: 20px;'>Error al actualizar el usuario: " . $e->getMessage() . "</div>";
        }
    } else {
        $mensaje = "<div style='color: red; font-weight: bold; margin-top: 20px;'>Por favor, complete todos los campos requeridos.</div>";
    }
}

// Obtener los datos actuales del usuario
try {
    $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE id = :id");
    $stmt->execute([':id' => $userId]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$user) {
        die("User not found");
    }
} catch (PDOException $e) {
    die("Error fetching user: " . $e->getMessage());
}

include './partials/layouts/layoutTop.php';
?>

<div class="dashboard-main-body">
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
        <h6 class="fw-semibold mb-0">Edit User</h6>
        <ul class="d-flex align-items-center gap-2">
            <li class="fw-medium">
                <a href="home.php" class="d-flex align-items-center gap-1 hover-text-primary">
                    <iconify-icon icon="solar:home-smile-angle-outline" class="icon text-lg"></iconify-icon>
                    Dashboard
                </a>
            </li>
            <li>-</li>
            <li class="fw-medium">Edit User</li>
        </ul>
    </div>

    <div class="card h-100 p-0 radius-12">
        <div class="card-body p-24">
            <div class="row justify-content-center">
                <div class="col-xxl-6 col-xl-8 col-lg-10">
                    <div class="card border">
                        <div class="card-body">
                            <h6 class="text-md text-primary-light mb-16">Edit User Details</h6>

                            <!-- Mensaje de éxito o error -->
                            <?php echo $mensaje; ?>

                            <form action="edit-user.php?id=<?php echo $userId; ?>" method="POST">
                                <input type="hidden" name="id" value="<?php echo $user['id']; ?>">
                                <div class="mb-20">
                                    <label for="nombre" class="form-label fw-semibold text-primary-light text-sm mb-8">Full Name <span class="text-danger-600">*</span></label>
                                    <input type="text" class="form-control radius-8" id="nombre" name="nombre" value="<?php echo htmlspecialchars($user['nombre']); ?>" placeholder="Enter Full Name" required>
                                </div>
                                <div class="mb-20">
                                    <label for="correo" class="form-label fw-semibold text-primary-light text-sm mb-8">Email <span class="text-danger-600">*</span></label>
                                    <input type="email" class="form-control radius-8" id="correo" name="correo" value="<?php echo htmlspecialchars($user['correo']); ?>" placeholder="Enter email address" required>
                                </div>
                                <div class="mb-20">
                                    <label for="capital_validator_x" class="form-label fw-semibold text-primary-light text-sm mb-8">Capital Validator X</label>
                                    <input type="text" class="form-control radius-8" id="capital_validator_x" name="capital_validator_x" value="<?php echo htmlspecialchars($user['capital_validator_x']); ?>" placeholder="Enter Capital Validator X" required>
                                </div>
                                <div class="mb-20">
                                    <label for="capital_trading_ia" class="form-label fw-semibold text-primary-light text-sm mb-8">Capital Trading IA</label>
                                    <input type="text" class="form-control radius-8" id="capital_trading_ia" name="capital_trading_ia" value="<?php echo htmlspecialchars($user['capital_trading_ia']); ?>" placeholder="Enter Capital Trading IA" required>
                                </div>
                                <div class="mb-20">
                                    <label for="rol" class="form-label fw-semibold text-primary-light text-sm mb-8">Role <span class="text-danger-600">*</span> </label>
                                    <select class="form-control radius-8 form-select" id="rol" name="rol" required>
                                        <option value="usuario" <?php if ($user['rol'] == 'usuario') echo 'selected'; ?>>Usuario</option>
                                        <option value="administrador" <?php if ($user['rol'] == 'administrador') echo 'selected'; ?>>Administrador</option>
                                    </select>
                                </div>
                                <div class="d-flex align-items-center justify-content-center gap-3">
                                    <button type="button" class="border border-danger-600 bg-hover-danger-200 text-danger-600 text-md px-56 py-11 radius-8" onclick="window.history.back();">
                                        Cancel
                                    </button>
                                    <button type="submit" class="btn btn-primary border border-primary-600 text-md px-56 py-12 radius-8">
                                        Save
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include './partials/layouts/layoutBottom.php'; ?>
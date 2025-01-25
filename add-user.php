<?php
session_start();
require_once 'config.php';
require_once 'auth.php';

// Verificar si el usuario está autenticado y es administrador
check_admin();

$mensaje = '';

// Manejar el envío del formulario
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre = $_POST['nombre'];
    $correo = $_POST['correo'];
    $contrasena = $_POST['contrasena'];
    $confirmar_contrasena = $_POST['confirmar_contrasena'];
    $rol = $_POST['rol'];
    $capital_validator_x = $_POST['capital_validator_x'];
    $capital_trading_ia = $_POST['capital_trading_ia'];

    // Validar que los campos requeridos no estén vacíos
    if (!empty($nombre) && !empty($correo) && !empty($contrasena) && !empty($confirmar_contrasena) && !empty($rol)) {
        if ($contrasena === $confirmar_contrasena) {
            $hashed_contrasena = password_hash($contrasena, PASSWORD_DEFAULT);
            try {
                $stmt = $pdo->prepare("INSERT INTO usuarios (nombre, correo, contrasena, rol, capital_validator_x, capital_trading_ia, created_at) VALUES (:nombre, :correo, :contrasena, :rol, :capital_validator_x, :capital_trading_ia, NOW())");
                $stmt->execute([
                    ':nombre' => $nombre,
                    ':correo' => $correo,
                    ':contrasena' => $hashed_contrasena,
                    ':rol' => $rol,
                    ':capital_validator_x' => $capital_validator_x,
                    ':capital_trading_ia' => $capital_trading_ia,
                ]);
                $mensaje = "<div style='color: green; font-weight: bold; margin-top: 20px;'>Usuario agregado exitosamente.</div>";
            } catch (PDOException $e) {
                $mensaje = "<div style='color: red; font-weight: bold; margin-top: 20px;'>Error al agregar el usuario: " . $e->getMessage() . "</div>";
            }
        } else {
            $mensaje = "<div style='color: red; font-weight: bold; margin-top: 20px;'>Las contraseñas no coinciden.</div>";
        }
    } else {
        $mensaje = "<div style='color: red; font-weight: bold; margin-top: 20px;'>Por favor, complete todos los campos requeridos.</div>";
    }
}

include './partials/layouts/layoutTop.php';
?>

<div class="dashboard-main-body">
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
        <h6 class="fw-semibold mb-0">Add User</h6>
        <ul class="d-flex align-items-center gap-2">
            <li class="fw-medium">
                <a href="index.php" class="d-flex align-items-center gap-1 hover-text-primary">
                    <iconify-icon icon="solar:home-smile-angle-outline" class="icon text-lg"></iconify-icon>
                    Dashboard
                </a>
            </li>
            <li>-</li>
            <li class="fw-medium">Add User</li>
        </ul>
    </div>

    <div class="card h-100 p-0 radius-12">
        <div class="card-body p-24">
            <div class="row justify-content-center">
                <div class="col-xxl-6 col-xl-8 col-lg-10">
                    <div class="card border">
                        <div class="card-body">
                            <!-- Mensaje de éxito o error -->
                            <?php echo $mensaje; ?>

                            <form action="add-user.php" method="POST">
                                <div class="mb-20">
                                    <label for="name" class="form-label fw-semibold text-primary-light text-sm mb-8">Full Name <span class="text-danger-600">*</span></label>
                                    <input type="text" class="form-control radius-8" id="name" name="nombre" placeholder="Enter Full Name" required>
                                </div>
                                <div class="mb-20">
                                    <label for="email" class="form-label fw-semibold text-primary-light text-sm mb-8">Email <span class="text-danger-600">*</span></label>
                                    <input type="email" class="form-control radius-8" id="email" name="correo" placeholder="Enter email address" required>
                                </div>
                                <div class="mb-20">
                                    <label for="contrasena" class="form-label fw-semibold text-primary-light text-sm mb-8">Password <span class="text-danger-600">*</span></label>
                                    <input type="password" class="form-control radius-8" id="contrasena" name="contrasena" placeholder="Enter password" required>
                                </div>
                                <div class="mb-20">
                                    <label for="confirmar_contrasena" class="form-label fw-semibold text-primary-light text-sm mb-8">Confirm Password <span class="text-danger-600">*</span></label>
                                    <input type="password" class="form-control radius-8" id="confirmar_contrasena" name="confirmar_contrasena" placeholder="Confirm password" required>
                                </div>
                                <div class="mb-20">
                                    <label for="rol" class="form-label fw-semibold text-primary-light text-sm mb-8">Role <span class="text-danger-600">*</span></label>
                                    <select class="form-control radius-8" id="rol" name="rol" required>
                                        <option value="usuario">Usuario</option>
                                        <option value="administrador">Administrador</option>
                                    </select>
                                </div>
                                <div class="mb-20">
                                    <label for="capital_validator_x" class="form-label fw-semibold text-primary-light text-sm mb-8">Capital Validator X</label>
                                    <input type="number" step="0.01" class="form-control radius-8" id="capital_validator_x" name="capital_validator_x" placeholder="Enter Capital Validator X">
                                </div>
                                <div class="mb-20">
                                    <label for="capital_trading_ia" class="form-label fw-semibold text-primary-light text-sm mb-8">Capital Trading IA</label>
                                    <input type="number" step="0.01" class="form-control radius-8" id="capital_trading_ia" name="capital_trading_ia" placeholder="Enter Capital Trading IA">
                                </div>
                                <div class="d-flex align-items-center justify-content-center gap-3">
                                    <button type="button" class="border border-danger-600 bg-hover-danger-200 text-danger-600 text-md px-56 py-11 radius-8" onclick="window.location.href='index.php'">
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

<?php include './partials/layouts/layoutBottom.php' ?>

<?php $script = '<script></script>';?>
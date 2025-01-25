<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Verificar si el usuario está autenticado
if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['usuario_id'];

// Incluir el archivo de configuración
require_once 'config.php';

$mensaje_exito_perfil = '';
$mensaje_exito_contrasena = '';

try {
    // Obtener datos del usuario autenticado
    $sql = "SELECT * FROM usuarios WHERE id = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':id', $user_id, PDO::PARAM_INT);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        $nombre = $user['nombre'];
        $correo = $user['correo'];
    } else {
        die("No se encontraron datos para el usuario.");
    }
} catch (PDOException $e) {
    die("Error al obtener los datos del usuario: " . $e->getMessage());
}

// Procesar el formulario de edición de perfil
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_profile'])) {
    $nuevo_nombre = $_POST['nombre'];
    $nuevo_correo = $_POST['correo'];

    try {
        $sql = "UPDATE usuarios SET nombre = :nombre, correo = :correo WHERE id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':nombre', $nuevo_nombre, PDO::PARAM_STR);
        $stmt->bindParam(':correo', $nuevo_correo, PDO::PARAM_STR);
        $stmt->bindParam(':id', $user_id, PDO::PARAM_INT);
        $stmt->execute();
        $mensaje_exito_perfil = "Perfil actualizado con éxito.";
    } catch (PDOException $e) {
        die("Error al actualizar el perfil: " . $e->getMessage());
    }
}

// Procesar el formulario de cambio de contraseña
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['change_password'])) {
    $nueva_contrasena = password_hash($_POST['nueva_contrasena'], PASSWORD_BCRYPT);

    try {
        $sql = "UPDATE usuarios SET contrasena = :contrasena WHERE id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':contrasena', $nueva_contrasena, PDO::PARAM_STR);
        $stmt->bindParam(':id', $user_id, PDO::PARAM_INT);
        $stmt->execute();
        $mensaje_exito_contrasena = "Contraseña cambiada con éxito.";
    } catch (PDOException $e) {
        die("Error al cambiar la contraseña: " . $e->getMessage());
    }
}
?>

<?php $script ='<script>
// ================== Password Show Hide Js Start ==========
function initializePasswordToggle(toggleSelector) {
    $(toggleSelector).on("click", function() {
        $(this).toggleClass("ri-eye-off-line");
        var input = $($(this).attr("data-toggle"));
        if (input.attr("type") === "password") {
            input.attr("type", "text");
        } else {
            input.attr("type", "password");
        }
    });
}
// Call the function
initializePasswordToggle(".toggle-password");
// ========================= Password Show Hide Js End ===========================
</script>';?>

<?php include './partials/layouts/layoutTop.php' ?>

<div class="dashboard-main-body">
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
        <h6 class="fw-semibold mb-0">View Profile</h6>
        <ul class="d-flex align-items-center gap-2">
            <li class="fw-medium">
                <a href="index.php" class="d-flex align-items-center gap-1 hover-text-primary">
                    <iconify-icon icon="solar:home-smile-angle-outline" class="icon text-lg"></iconify-icon>
                    Dashboard
                </a>
            </li>
            <li>-</li>
            <li class="fw-medium">View Profile</li>
        </ul>
    </div>

    <div class="row gy-4">
        <div class="col-lg-4">
            <div class="user-grid-card position-relative border radius-16 overflow-hidden bg-base h-100">
                <div class="pb-24 ms-16 mb-24 me-16 mt--100">
                    <div class="text-center border border-top-0 border-start-0 border-end-0">
                        <h6 class="mb-0 mt-16"><?php echo htmlspecialchars($nombre); ?></h6>
                        <span class="text-secondary-light mb-16"><?php echo htmlspecialchars($correo); ?></span>
                    </div>
                    <div class="mt-24">
                        <h6 class="text-xl mb-16">Personal Info</h6>
                        <ul>
                            <li class="d-flex align-items-center gap-1 mb-12">
                                <span class="w-30 text-md fw-semibold text-primary-light">Full Name</span>
                                <span class="w-70 text-secondary-light fw-medium">: <?php echo htmlspecialchars($nombre); ?></span>
                            </li>
                            <li class="d-flex align-items-center gap-1 mb-12">
                                <span class="w-30 text-md fw-semibold text-primary-light">Email</span>
                                <span class="w-70 text-secondary-light fw-medium">: <?php echo htmlspecialchars($correo); ?></span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-8">
            <div class="card h-100">
                <div class="card-body p-24">
                    <ul class="nav border-gradient-tab nav-pills mb-20 d-inline-flex" id="pills-tab" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link d-flex align-items-center px-24 active" id="pills-edit-profile-tab" data-bs-toggle="pill" data-bs-target="#pills-edit-profile" type="button" role="tab" aria-controls="pills-edit-profile" aria-selected="true">
                                Edit Profile
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link d-flex align-items-center px-24" id="pills-change-password-tab" data-bs-toggle="pill" data-bs-target="#pills-change-password" type="button" role="tab" aria-controls="pills-change-password" aria-selected="false" tabindex="-1">
                                Change Password
                            </button>
                        </li>
                    </ul>

                    <div class="tab-content" id="pills-tabContent">
                        <div class="tab-pane fade show active" id="pills-edit-profile" role="tabpanel" aria-labelledby="pills-edit-profile-tab" tabindex="0">
                            <?php if ($mensaje_exito_perfil): ?>
                                <div class="alert alert-success" role="alert">
                                    <?php echo $mensaje_exito_perfil; ?>
                                </div>
                            <?php endif; ?>
                            <form method="post" action="">
                                <div class="row">
                                    <div class="col-sm-6">
                                        <div class="mb-20">
                                            <label for="nombre" class="form-label fw-semibold text-primary-light text-sm mb-8">Full Name <span class="text-danger-600">*</span></label>
                                            <input type="text" class="form-control radius-8" id="nombre" name="nombre" value="<?php echo htmlspecialchars($nombre); ?>" placeholder="Enter Full Name" required>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="mb-20">
                                            <label for="correo" class="form-label fw-semibold text-primary-light text-sm mb-8">Email <span class="text-danger-600">*</span></label>
                                            <input type="email" class="form-control radius-8" id="correo" name="correo" value="<?php echo htmlspecialchars($correo); ?>" placeholder="Enter email address" required>
                                        </div>
                                    </div>
                                </div>
                                <div class="d-flex align-items-center justify-content-center gap-3">
                                    <button type="submit" name="update_profile" class="btn btn-primary border border-primary-600 text-md px-56 py-12 radius-8">
                                        Save
                                    </button>
                                </div>
                            </form>
                        </div>

                        <div class="tab-pane fade" id="pills-change-password" role="tabpanel" aria-labelledby="pills-change-password-tab" tabindex="0">
                            <?php if ($mensaje_exito_contrasena): ?>
                                <div class="alert alert-success" role="alert">
                                    <?php echo $mensaje_exito_contrasena; ?>
                                </div>
                            <?php endif; ?>
                            <form method="post" action="">
                                <div class="mb-20">
                                    <label for="nueva_contrasena" class="form-label fw-semibold text-primary-light text-sm mb-8">New Password <span class="text-danger-600">*</span></label>
                                    <div class="position-relative">
                                        <input type="password" class="form-control radius-8" id="nueva_contrasena" name="nueva_contrasena" placeholder="Enter New Password" required>
                                        <span class="toggle-password ri-eye-line cursor-pointer position-absolute end-0 top-50 translate-middle-y me-16 text-secondary-light" data-toggle="#nueva_contrasena"></span>
                                    </div>
                                </div>
                                <div class="mb-20">
                                    <label for="confirmar_contrasena" class="form-label fw-semibold text-primary-light text-sm mb-8">Confirm Password <span class="text-danger-600">*</span></label>
                                    <div class="position-relative">
                                        <input type="password" class="form-control radius-8" id="confirmar_contrasena" name="confirmar_contrasena" placeholder="Confirm Password" required>
                                        <span class="toggle-password ri-eye-line cursor-pointer position-absolute end-0 top-50 translate-middle-y me-16 text-secondary-light" data-toggle="#confirmar_contrasena"></span>
                                    </div>
                                </div>
                                <div class="d-flex align-items-center justify-content-center gap-3">
                                    <button type="submit" name="change_password" class="btn btn-primary border border-primary-600 text-md px-56 py-12 radius-8">
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
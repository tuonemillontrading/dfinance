<?php
include './partials/layouts/layoutTop.php';
require 'config.php';
require 'functions.php';

// Mensaje de éxito o error
$mensaje = '';

// Manejar la eliminación del usuario
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_user_id'])) {
    $id = $_POST['delete_user_id'];
    if (deleteUsuario($pdo, $id)) {
        $mensaje = '<div class="alert alert-success">User deleted successfully</div>';
    } else {
        $mensaje = '<div class="alert alert-danger">Error deleting user</div>';
    }
}

// Manejar la edición del usuario
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_user_id'])) {
    $id = $_POST['edit_user_id'];
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
            $mensaje = "<div class='alert alert-success'>Usuario actualizado exitosamente.</div>";
        } catch (PDOException $e) {
            $mensaje = "<div class='alert alert-danger'>Error al actualizar el usuario: " . $e->getMessage() . "</div>";
        }
    } else {
        $mensaje = "<div class='alert alert-danger'>Por favor, complete todos los campos requeridos.</div>";
    }
}

// Obtener usuarios
$usuarios = getUsuarios($pdo);
?>

<div class="dashboard-main-body">
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
        <h6 class="fw-semibold mb-0">Users</h6>
        <ul class="d-flex align-items-center gap-2">
            <li class="fw-medium">
                <a href="crud.php" class="d-flex align-items-center gap-1 hover-text-primary">
                    <iconify-icon icon="solar:home-smile-angle-outline" class="icon text-lg"></iconify-icon>
                    Dashboard
                </a>
            </li>
            <li>-</li>
            <li class="fw-medium">Settings - Users</li>
        </ul>
    </div>

    <?php echo $mensaje; ?>

    <div class="card h-100 p-0 radius-12">
        <div class="card-header border-bottom bg-base py-16 px-24 d-flex align-items-center flex-wrap gap-3 justify-content-between">
            <div class="d-flex align-items-center flex-wrap gap-3">
                <span class="text-md fw-medium text-secondary-light mb-0">Show</span>
                <select class="form-select form-select-sm w-auto ps-12 py-6 radius-12 h-40-px">
                    <option>1</option>
                    <option>2</option>
                    <option>3</option>
                    <option>4</option>
                    <option>5</option>
                    <option>6</option>
                    <option>7</option>
                    <option>8</option>
                    <option>9</option>
                    <option>10</option>
                </select>
                <form class="navbar-search" method="GET" action="crud.php">
                    <input type="text" class="bg-base h-40-px w-auto" name="search" placeholder="Search">
                    <iconify-icon icon="ion:search-outline" class="icon"></iconify-icon>
                </form>
            </div>

            <a href="add-user.php" class="btn btn-primary text-sm btn-sm px-12 py-12 radius-8 d-flex align-items-center gap-2">
                <iconify-icon icon="ic:baseline-plus" class="icon text-xl line-height-1"></iconify-icon>
                Add User
            </a>
        </div>
        <div class="card-body p-24">
            <div class="table-responsive scroll-sm">
                <table class="table bordered-table sm-table mb-0">
                    <thead>
                        <tr>
                            <th scope="col">ID</th>
                            <th scope="col" class="text-center">Name</th>
                            <th scope="col" class="text-center">Email</th>
                            <th scope="col" class="text-center">Role</th>
                            <th scope="col" class="text-center">Capital Validator X (USDT)</th>
                            <th scope="col" class="text-center">Capital Trading IA (USDT)</th>
                            <th scope="col" class="text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($usuarios as $usuario): ?>
                        <tr>
                            <td><?php echo $usuario['id']; ?></td>
                            <td class="text-center"><?php echo $usuario['nombre']; ?></td>
                            <td class="text-center"><?php echo $usuario['correo']; ?></td>
                            <td class="text-center"><?php echo $usuario['rol']; ?></td>
                            <td class="text-center"><?php echo $usuario['capital_validator_x']; ?> USDT</td>
                            <td class="text-center"><?php echo $usuario['capital_trading_ia']; ?> USDT</td>
                            <td class="text-center">
                                <div class="d-flex align-items-center gap-10 justify-content-center">
                                    <button type="button" class="bg-info-100 text-info-600 bg-hover-info-200 fw-medium w-40-px h-40-px d-flex justify-content-center align-items-center rounded-circle" data-bs-toggle="modal" data-bs-target="#viewUserModal<?php echo $usuario['id']; ?>">
                                        <iconify-icon icon="bi:eye" class="menu-icon"></iconify-icon>
                                    </button>
                                    <button type="button" class="bg-success-100 text-success-600 bg-hover-success-200 fw-medium w-40-px h-40-px d-flex justify-content-center align-items-center rounded-circle" data-bs-toggle="modal" data-bs-target="#editUserModal<?php echo $usuario['id']; ?>">
                                        <iconify-icon icon="lucide:edit" class="menu-icon"></iconify-icon>
                                    </button>
                                    <form method="POST" style="display:inline-block;">
                                        <input type="hidden" name="delete_user_id" value="<?php echo $usuario['id']; ?>">
                                        <button type="submit" class="bg-danger-focus bg-hover-danger-200 text-danger-600 fw-medium w-40-px h-40-px d-flex justify-content-center align-items-center rounded-circle">
                                            <iconify-icon icon="fluent:delete-24-regular" class="menu-icon"></iconify-icon>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>

                        <!-- Modal View User -->
                        <div class="modal fade" id="viewUserModal<?php echo $usuario['id']; ?>" tabindex="-1" aria-labelledby="viewUserModalLabel<?php echo $usuario['id']; ?>" aria-hidden="true">
                            <div class="modal-dialog modal-lg modal-dialog-centered">
                                <div class="modal-content radius-16 bg-base">
                                    <div class="modal-header py-16 px-24 border border-top-0 border-start-0 border-end-0">
                                        <h1 class="modal-title fs-5" id="viewUserModalLabel<?php echo $usuario['id']; ?>">View User</h1>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body p-24">
                                        <form>
                                            <div class="row">
                                                <div class="col-6 mb-20">
                                                    <label for="viewname<?php echo $usuario['id']; ?>" class="form-label fw-semibold text-primary-light text-sm mb-8">Name </label>
                                                    <input type="text" class="form-control radius-8" id="viewname<?php echo $usuario['id']; ?>" name="nombre" value="<?php echo $usuario['nombre']; ?>" readonly>
                                                </div>
                                                <div class="col-6 mb-20">
                                                    <label for="viewcorreo<?php echo $usuario['id']; ?>" class="form-label fw-semibold text-primary-light text-sm mb-8">Email </label>
                                                    <input type="email" class="form-control radius-8" id="viewcorreo<?php echo $usuario['id']; ?>" name="correo" value="<?php echo $usuario['correo']; ?>" readonly>
                                                </div>
                                                <div class="col-6 mb-20">
                                                    <label for="viewrole<?php echo $usuario['id']; ?>" class="form-label fw-semibold text-primary-light text-sm mb-8">Role </label>
                                                    <input type="text" class="form-control radius-8" id="viewrole<?php echo $usuario['id']; ?>" name="rol" value="<?php echo $usuario['rol']; ?>" readonly>
                                                </div>
                                                <div class="col-6 mb-20">
                                                    <label for="viewcapital_validator_x<?php echo $usuario['id']; ?>" class="form-label fw-semibold text-primary-light text-sm mb-8">Capital Validator X</label>
                                                    <input type="text" class="form-control radius-8" id="viewcapital_validator_x<?php echo $usuario['id']; ?>" name="capital_validator_x" value="<?php echo $usuario['capital_validator_x']; ?>" readonly>
                                                </div>
                                                <div class="col-6 mb-20">
                                                    <label for="viewcapital_trading_ia<?php echo $usuario['id']; ?>" class="form-label fw-semibold text-primary-light text-sm mb-8">Capital Trading IA</label>
                                                    <input type="text" class="form-control radius-8" id="viewcapital_trading_ia<?php echo $usuario['id']; ?>" name="capital_trading_ia" value="<?php echo $usuario['capital_trading_ia']; ?>" readonly>
                                                </div>
                                                <div class="d-flex align-items-center justify-content-center gap-3 mt-24">
                                                    <button type="button" class="btn btn-primary border border-primary-600 text-md px-50 py-12 radius-8" data-bs-dismiss="modal">
                                                        Close
                                                    </button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Modal Edit User -->
                        <div class="modal fade" id="editUserModal<?php echo $usuario['id']; ?>" tabindex="-1" aria-labelledby="editUserModalLabel<?php echo $usuario['id']; ?>" aria-hidden="true">
                            <div class="modal-dialog modal-lg modal-dialog-centered">
                                <div class="modal-content radius-16 bg-base">
                                    <div class="modal-header py-16 px-24 border border-top-0 border-start-0 border-end-0">
                                        <h1 class="modal-title fs-5" id="editUserModalLabel<?php echo $usuario['id']; ?>">Edit User</h1>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body p-24">
                                        <form method="POST" action="crud.php">
                                            <input type="hidden" name="edit_user_id" value="<?php echo $usuario['id']; ?>">
                                            <div class="row">
                                                <div class="col-6 mb-20">
                                                    <label for="editname<?php echo $usuario['id']; ?>" class="form-label fw-semibold text-primary-light text-sm mb-8">Name </label>
                                                    <input type="text" class="form-control radius-8" id="editname<?php echo $usuario['id']; ?>" name="nombre" value="<?php echo $usuario['nombre']; ?>" required>
                                                </div>
                                                <div class="col-6 mb-20">
                                                    <label for="editcorreo<?php echo $usuario['id']; ?>" class="form-label fw-semibold text-primary-light text-sm mb-8">Email </label>
                                                    <input type="email" class="form-control radius-8" id="editcorreo<?php echo $usuario['id']; ?>" name="correo" value="<?php echo $usuario['correo']; ?>" required>
                                                </div>
                                                <div class="col-6 mb-20">
                                                    <label for="editcapital_validator_x<?php echo $usuario['id']; ?>" class="form-label fw-semibold text-primary-light text-sm mb-8">Capital Validator X</label>
                                                    <input type="text" class="form-control radius-8" id="editcapital_validator_x<?php echo $usuario['id']; ?>" name="capital_validator_x" value="<?php echo $usuario['capital_validator_x']; ?>" required>
                                                </div>
                                                <div class="col-6 mb-20">
                                                    <label for="editcapital_trading_ia<?php echo $usuario['id']; ?>" class="form-label fw-semibold text-primary-light text-sm mb-8">Capital Trading IA</label>
                                                    <input type="text" class="form-control radius-8" id="editcapital_trading_ia<?php echo $usuario['id']; ?>" name="capital_trading_ia" value="<?php echo $usuario['capital_trading_ia']; ?>" required>
                                                </div>
                                                <div class="col-6 mb-20">
                                                    <label for="editrole<?php echo $usuario['id']; ?>" class="form-label fw-semibold text-primary-light text-sm mb-8">Role </label>
                                                    <select class="form-control radius-8 form-select" id="editrole<?php echo $usuario['id']; ?>" name="rol" required>
                                                        <option value="usuario" <?php echo $usuario['rol'] == 'usuario' ? 'selected' : ''; ?>>Usuario</option>
                                                        <option value="administrador" <?php echo $usuario['rol'] == 'administrador' ? 'selected' : ''; ?>>Administrador</option>
                                                    </select>
                                                </div>
                                                <div class="d-flex align-items-center justify-content-center gap-3 mt-24">
                                                    <button type="reset" class="border border-danger-600 bg-hover-danger-200 text-danger-600 text-md px-50 py-11 radius-8">
                                                        Cancel
                                                    </button>
                                                    <button type="submit" class="btn btn-primary border border-primary-600 text-md px-50 py-12 radius-8">
                                                        Update
                                                    </button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mt-24">
                <span>Showing 1 to 10 of <?php echo count($usuarios); ?> entries</span>
                <ul class="pagination d-flex flex-wrap align-items-center gap-2 justify-content-center">
                    <li class="page-item">
                        <a class="page-link bg-neutral-200 text-secondary-light fw-semibold radius-8 border-0 d-flex align-items-center justify-content-center h-32-px w-32-px text-md" href="javascript:void(0)">
                            <iconify-icon icon="ep:d-arrow-left" class=""></iconify-icon>
                        </a>
                    </li>
                    <li class="page-item">
                        <a class="page-link text-secondary-light fw-semibold radius-8 border-0 d-flex align-items-center justify-content-center h-32-px w-32-px text-md bg-primary-600 text-white" href="javascript:void(0)">1</a>
                    </li>
                    <li class="page-item">
                        <a class="page-link bg-neutral-200 text-secondary-light fw-semibold radius-8 border-0 d-flex align-items-center justify-content-center h-32-px w-32-px" href="javascript:void(0)">2</a>
                    </li>
                    <li class="page-item">
                        <a class="page-link bg-neutral-200 text-secondary-light fw-semibold radius-8 border-0 d-flex align-items-center justify-content-center h-32-px w-32-px text-md" href="javascript:void(0)">3</a>
                    </li>
                    <li class="page-item">
                        <a class="page-link bg-neutral-200 text-secondary-light fw-semibold radius-8 border-0 d-flex align-items-center justify-content-center h-32-px w-32-px text-md" href="javascript:void(0)">4</a>
                    </li>
                    <li class="page-item">
                        <a class="page-link bg-neutral-200 text-secondary-light fw-semibold radius-8 border-0 d-flex align-items-center justify-content-center h-32-px w-32-px text-md" href="javascript:void(0)">5</a>
                    </li>
                    <li class="page-item">
                        <a class="page-link bg-neutral-200 text-secondary-light fw-semibold radius-8 border-0 d-flex align-items-center justify-content-center h-32-px w-32-px text-md" href="javascript:void(0)">
                            <iconify-icon icon="ep:d-arrow-right" class=""></iconify-icon>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>

<?php include './partials/scripts.php' ?>

</body>
</html>
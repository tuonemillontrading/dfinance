<?php
require 'config.php';
require 'middleware.php';

check_admin();

// Obtener lista de usuarios
$sql = "SELECT * FROM usuarios";
$stmt = $pdo->prepare($sql);
$stmt->execute();
$usuarios = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Usuarios</title>
</head>
<body>
    <h1>Lista de Usuarios</h1>
    <table>
        <tr>
            <th>ID</th>
            <th>Nombre</th>
            <th>Correo</th>
            <th>Rol</th>
            <th>Acciones</th>
        </tr>
        <?php foreach ($usuarios as $usuario): ?>
            <tr>
                <td><?php echo htmlspecialchars($usuario['id']); ?></td>
                <td><?php echo htmlspecialchars($usuario['nombre']); ?></td>
                <td><?php echo htmlspecialchars($usuario['correo']); ?></td>
                <td><?php echo htmlspecialchars($usuario['rol']); ?></td>
                <td>
                    <a href="editar_usuario.php?id=<?php echo $usuario['id']; ?>">Editar</a>
                    <a href="eliminar_usuario.php?id=<?php echo $usuario['id']; ?>">Eliminar</a>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>
    <a href="dashboard.php">Volver al Dashboard</a>
</body>
</html>
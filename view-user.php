<?php
include 'config.php';

if (!isset($_GET['id'])) {
    die("User ID is required");
}

$userId = $_GET['id'];

try {
    $stmt = $pdo->prepare("SELECT u.id, u.nombre AS name, u.correo AS email, u.rol AS role, 
                                  u.capital_validator_x AS validator_x_capital, u.capital_trading_ia AS trading_ia_capital
                           FROM usuarios u
                           WHERE u.id = :id");
    $stmt->execute(['id' => $userId]);
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
    <h3>User Details</h3>
    <p><strong>Name:</strong> <?php echo htmlspecialchars($user['name']); ?></p>
    <p><strong>Email:</strong> <?php echo htmlspecialchars($user['email']); ?></p>
    <p><strong>Role:</strong> <?php echo htmlspecialchars($user['role']); ?></p>
    <p><strong>Validator X Capital:</strong> <?php echo htmlspecialchars($user['validator_x_capital']); ?></p>
    <p><strong>Trading IA Capital:</strong> <?php echo htmlspecialchars($user['trading_ia_capital']); ?></p>
</div>

<?php include './partials/layouts/layoutBottom.php'; ?>
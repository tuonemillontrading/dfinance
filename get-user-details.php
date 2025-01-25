<?php
include 'config.php';

if (!isset($_GET['id'])) {
    die("User ID is required");
}

$userId = $_GET['id'];

try {
    $stmt = $pdo->prepare("SELECT u.id, u.name, u.email, u.role, v.capital AS validator_x_capital, t.capital AS trading_ia_capital
                           FROM users u
                           LEFT JOIN validator_x v ON u.id = v.user_id
                           LEFT JOIN trading_ia t ON u.id = t.user_id
                           WHERE u.id = :id");
    $stmt->execute(['id' => $userId]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$user) {
        die("User not found");
    }
} catch (PDOException $e) {
    die("Error fetching user: " . $e->getMessage());
}

echo "<p><strong>Name:</strong> " . htmlspecialchars($user['name']) . "</p>";
echo "<p><strong>Email:</strong> " . htmlspecialchars($user['email']) . "</p>";
echo "<p><strong>Role:</strong> " . htmlspecialchars($user['role']) . "</p>";
echo "<p><strong>Validator X Capital:</strong> " . htmlspecialchars($user['validator_x_capital']) . "</p>";
echo "<p><strong>Trading IA Capital:</strong> " . htmlspecialchars($user['trading_ia_capital']) . "</p>";
?>
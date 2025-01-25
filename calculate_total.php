<?php
require 'config.php';

$system = $_POST['system'];
$percentage = $_POST['percentage'];
$totalToPay = 0;

if ($system === 'validator') {
    $stmt = $pdo->prepare("SELECT SUM(capital_validator_x) as totalCapital FROM usuarios WHERE capital_validator_x > 0");
} else if ($system === 'trading') {
    $stmt = $pdo->prepare("SELECT SUM(capital_trading_ia) as totalCapital FROM usuarios WHERE capital_trading_ia > 0");
}

$stmt->execute();
$result = $stmt->fetch(PDO::FETCH_ASSOC);

if ($result) {
    $totalCapital = $result['totalCapital'];
    $totalToPay = $totalCapital * ($percentage / 100);
}

echo json_encode(['totalToPay' => $totalToPay]);
?>
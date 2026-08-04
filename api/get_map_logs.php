<?php
require_once '../config/database.php';
header('Content-Type: application/json');

$campus = $_GET['campus'] ?? 'manila';

$stmt = $pdo->prepare("SELECT * FROM crowd_traffic_logs WHERE campus = ?");
$stmt->execute([$campus]);
echo json_encode($stmt->fetchAll());
?>
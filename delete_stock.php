<?php
include 'db.php';

$response = ['success' => false];

if (isset($_GET['name'])) {
    $name = $_GET['name'];
    $sql = "DELETE FROM Stocks WHERE produit_id = (SELECT id FROM Produits WHERE nom = :name)";
    $stmt = $conn->prepare($sql);
    $stmt->execute(['name' => $name]);
    $response['success'] = true;
}

echo json_encode($response);
?>
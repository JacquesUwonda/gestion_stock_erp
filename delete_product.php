<?php
include 'db.php';

$response = ['success' => false];

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $sql = "DELETE FROM Produits WHERE id = :id";
    $stmt = $conn->prepare($sql);
    $stmt->execute(['id' => $id]);
    $response['success'] = true;
}

echo json_encode($response);
?>
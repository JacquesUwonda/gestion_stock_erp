<?php
include 'db.php';

$response = ['success' => false, 'message' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $products = $_POST['product'];
    $quantities = $_POST['quantity'];
    $totals = $_POST['total'];

    foreach ($products as $index => $productId) {
        $quantite = $quantities[$index];
        $montant_total = $totals[$index];

        $sql = "INSERT INTO Ventes (produit_id, quantite, montant_total) VALUES (:produit_id, :quantite, :montant_total)";
        $stmt = $conn->prepare($sql);
        $stmt->execute(['produit_id' => $productId, 'quantite' => $quantite, 'montant_total' => $montant_total]);
    }

    $response['success'] = true;
    $response['message'] = 'Ventes enregistrées avec succès !';
}

echo json_encode($response);
?>
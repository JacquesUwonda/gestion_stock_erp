<?php
include 'db.php';

$response = ['success' => false, 'message' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['saleId'] ?? null;
    $date_vente = $_POST['date_vente'];
    $produit_id = $_POST['produit_id'];
    $quantite = $_POST['quantite'];
    $montant_total = $_POST['montant_total'];

    // Vérifier si le stock est suffisant
    $sql = "SELECT quantite FROM Stocks WHERE produit_id = :produit_id";
    $stmt = $conn->prepare($sql);
    $stmt->execute(['produit_id' => $produit_id]);
    $stock = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($stock && $stock['quantite'] >= $quantite) {
        if ($id) {
            // Modification
            $sql = "UPDATE Ventes SET date_vente = :date_vente, produit_id = :produit_id, quantite = :quantite, montant_total = :montant_total WHERE id = :id";
            $stmt = $conn->prepare($sql);
            $stmt->execute(['date_vente' => $date_vente, 'produit_id' => $produit_id, 'quantite' => $quantite, 'montant_total' => $montant_total, 'id' => $id]);
        } else {
            // Ajout
            $sql = "INSERT INTO Ventes (date_vente, produit_id, quantite, montant_total) VALUES (:date_vente, :produit_id, :quantite, :montant_total)";
            $stmt = $conn->prepare($sql);
            $stmt->execute(['date_vente' => $date_vente, 'produit_id' => $produit_id, 'quantite' => $quantite, 'montant_total' => $montant_total]);
        }

        // Mettre à jour le stock
        $newQuantite = $stock['quantite'] - $quantite;
        $sql = "UPDATE Stocks SET quantite = :quantite WHERE produit_id = :produit_id";
        $stmt = $conn->prepare($sql);
        $stmt->execute(['quantite' => $newQuantite, 'produit_id' => $produit_id]);

        $response['success'] = true;
        $response['message'] = 'Vente enregistrée avec succès !';
    } else {
        $response['message'] = 'Stock insuffisant pour ce produit.';
    }
}

echo json_encode($response);
?>
<?php
include 'db.php';

$response = ['success' => false, 'message' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['supplyId'] ?? null;
    $date_approvisionnement = $_POST['date_approvisionnement'];
    $produit_id = $_POST['produit_id'];
    $quantite = $_POST['quantite'];
    $fournisseur_id = $_POST['fournisseur_id'];

    if ($id) {
        // Modification
        $sql = "UPDATE Approvisionnements SET date_approvisionnement = :date_approvisionnement, produit_id = :produit_id, quantite = :quantite, fournisseur_id = :fournisseur_id WHERE id = :id";
        $stmt = $conn->prepare($sql);
        $stmt->execute(['date_approvisionnement' => $date_approvisionnement, 'produit_id' => $produit_id, 'quantite' => $quantite, 'fournisseur_id' => $fournisseur_id, 'id' => $id]);
        $response['success'] = true;
        $response['message'] = 'Approvisionnement modifié avec succès !';
    } else {
        // Ajout
        $sql = "INSERT INTO Approvisionnements (date_approvisionnement, produit_id, quantite, fournisseur_id) VALUES (:date_approvisionnement, :produit_id, :quantite, :fournisseur_id)";
        $stmt = $conn->prepare($sql);
        $stmt->execute(['date_approvisionnement' => $date_approvisionnement, 'produit_id' => $produit_id, 'quantite' => $quantite, 'fournisseur_id' => $fournisseur_id]);
        $response['success'] = true;
        $response['message'] = 'Approvisionnement ajouté avec succès !';
    }
}

echo json_encode($response);
?>
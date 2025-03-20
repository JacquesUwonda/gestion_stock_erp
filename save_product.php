<?php
include 'db.php';

$response = ['success' => false, 'message' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['productId'] ?? null;
    $nom = $_POST['nom'];
    $description = $_POST['description'];
    $prix = $_POST['prix'];
    $quantite = $_POST['quantite'];
    $fournisseur_id = $_POST['fournisseur_id'];

    // Démarrer une transaction pour garantir la cohérence
    $conn->beginTransaction();

    try {
        if ($id) {
            // Modification du produit
            $sql = "UPDATE Produits SET nom = :nom, description = :description, prix = :prix, quantite_en_stock = :quantite, fournisseur_id = :fournisseur_id WHERE id = :id";
            $stmt = $conn->prepare($sql);
            $stmt->execute(['nom' => $nom, 'description' => $description, 'prix' => $prix, 'quantite' => $quantite, 'fournisseur_id' => $fournisseur_id, 'id' => $id]);
        } else {
            // Ajout d'un nouveau produit
            $sql = "INSERT INTO Produits (nom, description, prix, quantite_en_stock, fournisseur_id) VALUES (:nom, :description, :prix, :quantite, :fournisseur_id)";
            $stmt = $conn->prepare($sql);
            $stmt->execute(['nom' => $nom, 'description' => $description, 'prix' => $prix, 'quantite' => $quantite, 'fournisseur_id' => $fournisseur_id]);
        }

        // Valider la transaction
        $conn->commit();

        $response['success'] = true;
        $response['message'] = 'Produit enregistré avec succès !';
    } catch (PDOException $e) {
        // Annuler la transaction en cas d'erreur
        $conn->rollBack();
        $response['message'] = 'Erreur lors de l\'enregistrement du produit : ' . $e->getMessage();
    }
}

echo json_encode($response);
?>
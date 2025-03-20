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

    if ($id) {
        // Modification
        $sql = "UPDATE Produits SET nom = :nom, description = :description, prix = :prix, quantite_en_stock = :quantite, fournisseur_id = :fournisseur_id WHERE id = :id";
        $stmt = $conn->prepare($sql);
        $stmt->execute(['nom' => $nom, 'description' => $description, 'prix' => $prix, 'quantite' => $quantite, 'fournisseur_id' => $fournisseur_id, 'id' => $id]);
        $response['success'] = true;
        $response['message'] = 'Produit modifié avec succès !';
    } else {
        // Ajout
        $sql = "INSERT INTO Produits (nom, description, prix, quantite_en_stock, fournisseur_id) VALUES (:nom, :description, :prix, :quantite, :fournisseur_id)";
        $stmt = $conn->prepare($sql);
        $stmt->execute(['nom' => $nom, 'description' => $description, 'prix' => $prix, 'quantite' => $quantite, 'fournisseur_id' => $fournisseur_id]);
        $response['success'] = true;
        $response['message'] = 'Produit ajouté avec succès !';
    }
}

echo json_encode($response);
?>
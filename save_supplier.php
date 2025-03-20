<?php
include 'db.php';

$response = ['success' => false, 'message' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['supplierId'] ?? null;
    $nom = $_POST['nom'];
    $adresse = $_POST['adresse'];
    $telephone = $_POST['telephone'];

    if ($id) {
        // Modification
        $sql = "UPDATE Fournisseurs SET nom = :nom, adresse = :adresse, telephone = :telephone WHERE id = :id";
        $stmt = $conn->prepare($sql);
        $stmt->execute(['nom' => $nom, 'adresse' => $adresse, 'telephone' => $telephone, 'id' => $id]);
        $response['success'] = true;
        $response['message'] = 'Fournisseur modifié avec succès !';
    } else {
        // Ajout
        $sql = "INSERT INTO Fournisseurs (nom, adresse, telephone) VALUES (:nom, :adresse, :telephone)";
        $stmt = $conn->prepare($sql);
        $stmt->execute(['nom' => $nom, 'adresse' => $adresse, 'telephone' => $telephone]);
        $response['success'] = true;
        $response['message'] = 'Fournisseur ajouté avec succès !';
    }
}

echo json_encode($response);
?>
<?php
include 'db.php';

$response = ['success' => false, 'message' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $produit_id = $_POST['produit_id'];
    $quantite = $_POST['quantite'];

    // Démarrer une transaction pour garantir la cohérence
    $conn->beginTransaction();

    try {
        // Vérifier si le stock existe déjà pour ce produit
        $sql = "SELECT * FROM Stocks WHERE produit_id = :produit_id";
        $stmt = $conn->prepare($sql);
        $stmt->execute(['produit_id' => $produit_id]);
        $stock = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($stock) {
            // Mettre à jour le stock existant
            $newQuantite = $stock['quantite'] + $quantite;

            // Vérifier que la quantité ne devient pas négative
            if ($newQuantite < 0) {
                throw new Exception('La quantité en stock ne peut pas être négative.');
            }

            $sql = "UPDATE Stocks SET quantite = :quantite WHERE produit_id = :produit_id";
            $stmt = $conn->prepare($sql);
            $stmt->execute(['quantite' => $newQuantite, 'produit_id' => $produit_id]);
        } else {
            // Ajouter un nouveau stock
            if ($quantite < 0) {
                throw new Exception('La quantité en stock ne peut pas être négative.');
            }

            $sql = "INSERT INTO Stocks (produit_id, quantite) VALUES (:produit_id, :quantite)";
            $stmt = $conn->prepare($sql);
            $stmt->execute(['produit_id' => $produit_id, 'quantite' => $quantite]);
        }

        // Valider la transaction
        $conn->commit();

        $response['success'] = true;
        $response['message'] = 'Stock mis à jour avec succès !';
    } catch (Exception $e) {
        // Annuler la transaction en cas d'erreur
        $conn->rollBack();
        $response['message'] = $e->getMessage();
    }
}

echo json_encode($response);
?>
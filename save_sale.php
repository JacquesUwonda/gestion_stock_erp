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
        // Démarrer une transaction pour garantir la cohérence
        $conn->beginTransaction();

        try {
            if ($id) {
                // Modification de la vente
                $sql = "UPDATE Ventes SET date_vente = :date_vente, produit_id = :produit_id, quantite = :quantite, montant_total = :montant_total WHERE id = :id";
                $stmt = $conn->prepare($sql);
                $stmt->execute(['date_vente' => $date_vente, 'produit_id' => $produit_id, 'quantite' => $quantite, 'montant_total' => $montant_total, 'id' => $id]);
            } else {
                // Ajout d'une nouvelle vente
                $sql = "INSERT INTO Ventes (date_vente, produit_id, quantite, montant_total) VALUES (:date_vente, :produit_id, :quantite, :montant_total)";
                $stmt = $conn->prepare($sql);
                $stmt->execute(['date_vente' => $date_vente, 'produit_id' => $produit_id, 'quantite' => $quantite, 'montant_total' => $montant_total]);
            }

            // Mettre à jour le stock
            $newQuantite = $stock['quantite'] - $quantite;
            $sql = "UPDATE Stocks SET quantite = :quantite WHERE produit_id = :produit_id";
            $stmt = $conn->prepare($sql);
            $stmt->execute(['quantite' => $newQuantite, 'produit_id' => $produit_id]);

            // Valider la transaction
            $conn->commit();

            $response['success'] = true;
            $response['message'] = 'Vente enregistrée avec succès !';
        } catch (PDOException $e) {
            // Annuler la transaction en cas d'erreur
            $conn->rollBack();
            $response['message'] = 'Erreur lors de l\'enregistrement de la vente : ' . $e->getMessage();
        }
    } else {
        $response['message'] = 'Stock insuffisant pour ce produit.';
    }
}

echo json_encode($response);
?>
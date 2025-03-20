<?php
include 'db.php';

// Récupérer les données du formulaire
$vente_id = $_POST['vente_id'];
$montant_paye = $_POST['montant_paye'];
$mode_paiement = $_POST['mode_paiement'];
$date_paiement = date('Y-m-d H:i:s');

try {
    // Vérifier si le montant payé est valide
    if ($montant_paye <= 0) {
        throw new Exception("Le montant payé doit être supérieur à zéro.");
    }

    // Insérer le paiement dans la base de données
    $sql = "INSERT INTO Paiements (vente_id, montant_paye, mode_paiement, date_paiement) VALUES (:vente_id, :montant_paye, :mode_paiement, :date_paiement)";
    $stmt = $conn->prepare($sql);
    $stmt->execute([
        ':vente_id' => $vente_id,
        ':montant_paye' => $montant_paye,
        ':mode_paiement' => $mode_paiement,
        ':date_paiement' => $date_paiement
    ]);

    // Retourner une réponse JSON en cas de succès
    echo json_encode(['success' => true, 'message' => 'Paiement enregistré avec succès !']);
} catch (PDOException $e) {
    // Retourner une réponse JSON en cas d'erreur PDO
    echo json_encode(['success' => false, 'message' => 'Erreur lors de l\'enregistrement du paiement : ' . $e->getMessage()]);
} catch (Exception $e) {
    // Retourner une réponse JSON en cas d'erreur générale
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>
<?php
include 'db.php';

$vente_id = $_POST['vente_id'];
$montant_paye = $_POST['montant_paye'];
$mode_paiement = $_POST['mode_paiement'];
$date_paiement = date('Y-m-d H:i:s');

try {
    $sql = "INSERT INTO Paiements (vente_id, montant_paye, mode_paiement, date_paiement) VALUES (:vente_id, :montant_paye, :mode_paiement, :date_paiement)";
    $stmt = $conn->prepare($sql);
    $stmt->execute([
        ':vente_id' => $vente_id,
        ':montant_paye' => $montant_paye,
        ':mode_paiement' => $mode_paiement,
        ':date_paiement' => $date_paiement
    ]);

    echo json_encode(['success' => true, 'message' => 'Paiement enregistré avec succès !']);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Erreur lors de l\'enregistrement du paiement : ' . $e->getMessage()]);
}
?>
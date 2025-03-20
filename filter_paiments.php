<?php
include 'db.php';

$sql = "SELECT Ventes.date_vente, Paiements.montant, Paiements.mode_paiement, (Ventes.montant_total - SUM(Paiements.montant)) AS solde_restant FROM Paiements JOIN Ventes ON Paiements.vente_id = Ventes.id GROUP BY Paiements.vente_id";
$stmt = $conn->query($sql);
$payments = $stmt->fetchAll(PDO::FETCH_ASSOC);

header('Content-Type: application/json');
echo json_encode($payments);
?>
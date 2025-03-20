<?php
include 'db.php';

$filterDate = $_GET['date'] ?? '';
$filterVente = $_GET['vente'] ?? '';

$sql = "SELECT Paiements.*, Ventes.date_vente, Ventes.montant_total FROM Paiements JOIN Ventes ON Paiements.vente_id = Ventes.id WHERE 1=1";

if (!empty($filterDate)) {
    $sql .= " AND DATE(Paiements.date_paiement) = :filterDate";
}
if (!empty($filterVente)) {
    $sql .= " AND Paiements.vente_id = :filterVente";
}

$stmt = $conn->prepare($sql);

if (!empty($filterDate)) {
    $stmt->bindParam(':filterDate', $filterDate);
}
if (!empty($filterVente)) {
    $stmt->bindParam(':filterVente', $filterVente);
}

$stmt->execute();
$payments = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($payments);
?>
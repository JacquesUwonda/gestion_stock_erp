<?php
include 'db.php';

$date = $_GET['date'] ?? '';
$product = $_GET['product'] ?? '';

$sql = "SELECT Ventes.date_vente, Produits.nom AS produit_nom, Ventes.quantite, Ventes.montant_total FROM Ventes JOIN Produits ON Ventes.produit_id = Produits.id WHERE 1=1";

if (!empty($date)) {
    $sql .= " AND Ventes.date_vente = :date";
}

if (!empty($product)) {
    $sql .= " AND Ventes.produit_id = :product";
}

$stmt = $conn->prepare($sql);

if (!empty($date)) {
    $stmt->bindParam(':date', $date);
}

if (!empty($product)) {
    $stmt->bindParam(':product', $product);
}

$stmt->execute();
$ventes = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($ventes);
?>
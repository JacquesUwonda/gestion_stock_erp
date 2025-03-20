<?php
include 'db.php';

// Récupérer les IDs des ventes sélectionnées
$selectedSales = $_GET['selectedSales'] ?? '';
$selectedSales = explode(',', $selectedSales);

// Récupérer les détails des ventes sélectionnées
$sql = "SELECT Produits.nom, Ventes.quantite, Ventes.montant_total 
        FROM Ventes 
        JOIN Produits ON Ventes.produit_id = Produits.id 
        WHERE Ventes.id IN (" . implode(',', array_map('intval', $selectedSales)) . ")";
$stmt = $conn->query($sql);
$ventes = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Retourner les données au format JSON
echo json_encode($ventes);
?>
<?php
include 'db.php';

// Récupérer les données des stocks
$sql = "SELECT Produits.nom, Stocks.quantite 
        FROM Stocks 
        JOIN Produits ON Stocks.produit_id = Produits.id";
$stmt = $conn->query($sql);
$stocks = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($stocks);
?>
<?php
$host = 'localhost';
$dbname = 'gestion_stock';
$username = 'root'; // Utilisateur par défaut de XAMPP
$password = ''; // Mot de passe par défaut de XAMPP

try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "<script>showNotification('error', 'Erreur de connexion à la base de données.');</script>";
}
?>
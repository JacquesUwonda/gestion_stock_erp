<?php
include 'db.php';

$paymentId = $_GET['id'];

try {
    $sql = "DELETE FROM Paiements WHERE id = :paymentId";
    $stmt = $conn->prepare($sql);
    $stmt->execute([':paymentId' => $paymentId]);

    echo json_encode(['success' => true, 'message' => 'Paiement supprimé avec succès !']);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Erreur lors de la suppression du paiement : ' . $e->getMessage()]);
}
?>
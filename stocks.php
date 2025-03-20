<?php include 'db.php'; ?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des stocks</title>
    <link rel="stylesheet" href="style.css">
    <!-- SweetAlert2 CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <!-- Font Awesome pour les icônes -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <!-- Navbar -->
    <div class="navbar">
        <div>
            <a href="index.php">Accueil</a>
            <a href="produits.php">Produits</a>
            <a href="fournisseurs.php">Fournisseurs</a>
            <a href="ventes.php">Ventes</a>
            <a href="approvisionnements.php">Approvisionnements</a>
            <a href="stocks.php">Stocks</a>
        </div>
    </div>

    <!-- Contenu principal -->
    <div class="container">
        <h1>Gestion des stocks</h1>

        <!-- Formulaire d'ajout de stock -->
        <form action="stocks.php" method="post" id="stockForm">
            <label for="produit_id">Produit :</label>
            <select id="produit_id" name="produit_id" required>
                <?php
                $sql = "SELECT id, nom FROM Produits";
                $stmt = $conn->query($sql);
                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    echo "<option value='{$row['id']}'>{$row['nom']}</option>";
                }
                ?>
            </select><br>
            <label for="quantite">Quantité :</label>
            <input type="number" id="quantite" name="quantite" required><br>
            <button type="submit" name="ajouter">Ajouter au stock</button>
        </form>

        <!-- Tableau des stocks -->
        <h2>Liste des stocks</h2>
        <table>
            <thead>
                <tr>
                    <th>Produit</th>
                    <th>Quantité en stock</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $sql = "SELECT Produits.nom AS produit_nom, Stocks.quantite FROM Stocks JOIN Produits ON Stocks.produit_id = Produits.id";
                $stmt = $conn->query($sql);
                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    echo "<tr>
                            <td>{$row['produit_nom']}</td>
                            <td>{$row['quantite']}</td>
                          </tr>";
                }
                ?>
            </tbody>
        </table>
    </div>

    <!-- SweetAlert2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
    // Fonction pour afficher une notification avec une promesse
    function showNotification(type, message) {
        return Swal.fire({
            icon: type, // 'success', 'error', 'warning', 'info'
            title: message,
            showConfirmButton: false,
            timer: 2000 // Fermer automatiquement après 2 secondes
        });
    }

    // Soumission du formulaire
    document.getElementById('stockForm').addEventListener('submit', function (e) {
        e.preventDefault(); // Empêcher le rechargement de la page
        const formData = new FormData(this);

        fetch('save_stock.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showNotification('success', data.message).then(() => {
                    window.location.reload(); // Recharger la page après la notification
                });
            } else {
                showNotification('error', data.message);
            }
        });
    });
    </script>
</body>
</html>
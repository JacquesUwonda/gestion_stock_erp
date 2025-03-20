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
            <a href="paiements.php">Paiements</a>

        </div>
    </div>

    <!-- Contenu principal -->
    <div class="container">
        <h1>Gestion des stocks</h1>

        <!-- Formulaire d'ajout de stock -->
        <form action="save_stock.php" method="post" id="stockForm">
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
            <input type="number" id="quantite" name="quantite" min="1" required><br>
            <button type="submit" name="ajouter">Ajouter au stock</button>
        </form>

        <!-- Tableau des stocks -->
        <h2>Liste des stocks</h2>
        <table id="stockTable">
            <thead>
                <tr>
                    <th>Produit</th>
                    <th>Quantité en stock</th>
                    <th>Statut</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $sql = "SELECT Produits.nom AS produit_nom, Stocks.quantite FROM Stocks JOIN Produits ON Stocks.produit_id = Produits.id";
                $stmt = $conn->query($sql);
                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    $status = ($row['quantite'] > 0) ? 'Disponible' : 'Rupture';
                    echo "<tr>
                            <td>{$row['produit_nom']}</td>
                            <td>{$row['quantite']}</td>
                            <td>{$status}</td>
                          </tr>";
                }
                ?>
            </tbody>
        </table>

        <!-- Bouton pour générer la fiche de stock -->
        <button id="generateStockReport">Générer la fiche de stock</button>
    </div>

    <!-- SweetAlert2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        // Fonction pour afficher une notification
        function showNotification(type, message) {
            return Swal.fire({
                icon: type, // 'success', 'error', 'warning', 'info'
                title: message,
                showConfirmButton: false,
                timer: 2000 // Fermer automatiquement après 2 secondes
            });
        }

        // Soumission du formulaire d'ajout de stock
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

        // Génération de la fiche de stock
        document.getElementById('generateStockReport').addEventListener('click', async function () {
    // Récupérer les données des stocks depuis l'API
    const response = await fetch('generate_stock_report.php');
    const stocks = await response.json();

    // Ouvrir une nouvelle fenêtre pour l'impression
    const printWindow = window.open('', '', 'height=600,width=800');
    printWindow.document.write('<html><head><title>Fiche de stock</title><style>');
    printWindow.document.write(`
        body { font-family: Arial, sans-serif; margin: 0; padding: 20px; }
        h1 { text-align: center; font-size: 24px; color: #333; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #000; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        .remarks { margin-top: 20px; }
        .signature { margin-top: 40px; }
    `);
    printWindow.document.write('</style></head><body>');
    printWindow.document.write('<h1>Fiche de stock</h1>');
    printWindow.document.write('<p><strong>Date :</strong> ' + new Date().toLocaleDateString() + '</p>');
    printWindow.document.write('<p><strong>Responsable :</strong> ________________________</p>');
    printWindow.document.write('<table>');
    printWindow.document.write('<thead><tr><th>Produit</th><th>Quantité en stock</th><th>Statut</th><th>Observations</th></tr></thead>');
    printWindow.document.write('<tbody>');

    // Ajouter les données des stocks
    stocks.forEach(stock => {
        const status = stock.quantite > 0 ? 'Disponible' : 'Rupture';
        printWindow.document.write(`
            <tr>
                <td>${stock.nom}</td>
                <td>${stock.quantite}</td>
                <td>${status}</td>
                <td></td>
            </tr>
        `);
    });

    printWindow.document.write('</tbody></table>');
    printWindow.document.write('<div class="remarks"><strong>Remarques :</strong><br>________________________________________________________________________<br>________________________________________________________________________<br>________________________________________________________________________</div>');
    printWindow.document.write('<div class="signature"><strong>Signature :</strong> ________________________</div>');
    printWindow.document.write('</body></html>');
    printWindow.document.close();
    printWindow.print();
});
    </script>
</body>
</html>
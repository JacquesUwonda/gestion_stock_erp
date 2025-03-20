<?php include 'db.php'; ?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des ventes</title>
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
        <h1>Gestion des ventes</h1>

        <!-- Formulaire de ventes multiples -->
        <h2>Enregistrement multiple de ventes</h2>
        <form id="multiSalesForm">
            <table id="salesTable">
                <thead>
                    <tr>
                        <th>Produit</th>
                        <th>Quantité</th>
                        <th>Prix unitaire</th>
                        <th>Total</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>
                            <select class="productSelect" name="product[]" required>
                                <?php
                                $sql = "SELECT id, nom, prix FROM Produits";
                                $stmt = $conn->query($sql);
                                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                    echo "<option value='{$row['id']}' data-prix='{$row['prix']}'>{$row['nom']}</option>";
                                }
                                ?>
                            </select>
                        </td>
                        <td><input type="number" class="quantity" name="quantity[]" min="1" required></td>
                        <td><input type="number" class="unitPrice" name="unitPrice[]" readonly></td>
                        <td><input type="number" class="total" name="total[]" readonly></td>
                        <td><button type="button" class="removeRow">Supprimer</button></td>
                    </tr>
                </tbody>
            </table>
            <button type="button" id="addRow">Ajouter une ligne</button>
            <button type="submit">Enregistrer les ventes</button>
        </form>

        <!-- Filtres pour le suivi des ventes -->
        <h2>Suivi des ventes</h2>
        <div class="filters">
            <label for="filterDate">Date :</label>
            <input type="date" id="filterDate">
            <label for="filterProduct">Produit :</label>
            <select id="filterProduct">
                <option value="">Tous les produits</option>
                <?php
                $sql = "SELECT id, nom FROM Produits";
                $stmt = $conn->query($sql);
                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    echo "<option value='{$row['id']}'>{$row['nom']}</option>";
                }
                ?>
            </select>
            <button id="applyFilters">Appliquer</button>
        </div>

        <!-- Tableau récapitulatif des ventes -->
<table id="salesSummary">
    <thead>
        <tr>
            <th>Sélection</th> <!-- Nouvelle colonne pour la sélection -->
            <th>Date</th>
            <th>Produit</th>
            <th>Quantité</th>
            <th>Montant total</th>
        </tr>
    </thead>
    <tbody>
        <?php
        $sql = "SELECT Ventes.id, Ventes.date_vente, Produits.nom AS produit_nom, Ventes.quantite, Ventes.montant_total 
                FROM Ventes 
                JOIN Produits ON Ventes.produit_id = Produits.id";
        $stmt = $conn->query($sql);
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            echo "<tr>
                    <td><input type='checkbox' class='saleCheckbox' data-id='{$row['id']}'></td>
                    <td>{$row['date_vente']}</td>
                    <td>{$row['produit_nom']}</td>
                    <td>{$row['quantite']}</td>
                    <td>{$row['montant_total']} €</td>
                  </tr>";
        }
        ?>
    </tbody>
</table>

        <!-- Bouton pour imprimer un reçu -->
<button id="printReceipt">Imprimer le reçu</button>

<!-- Reçu -->
<div id="receipt" style="display: none;">
    <h2>Reçu de vente</h2>
    <p>Nom de l'entreprise : <strong>Jacques ERP</strong></p>
    <p>Date : <?php echo date('d/m/Y H:i'); ?></p>
    <p>Mode de paiement : <span id="receiptPaymentMethod"></span></p>
    <table>
        <thead>
            <tr>
                <th>Produit</th>
                <th>Quantité</th>
                <th>Prix unitaire</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            <?php
            // Récupérer les détails de la vente depuis la base de données
            $sql = "SELECT Produits.nom, Ventes.quantite, Ventes.montant_total 
                    FROM Ventes 
                    JOIN Produits ON Ventes.produit_id = Produits.id 
                    ORDER BY Ventes.date_vente DESC 
                    LIMIT 1"; // On récupère la dernière vente
            $stmt = $conn->query($sql);
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $prixUnitaire = ($row['montant_total'] / $row['quantite']);
                echo "<tr>
                        <td>{$row['nom']}</td>
                        <td>{$row['quantite']}</td>
                        <td>" . number_format($prixUnitaire, 2) . " €</td>
                        <td>" . number_format($row['montant_total'], 2) . " €</td>
                      </tr>";
            }
            ?>
        </tbody>
    </table>
    <p>Total général : <strong><span id="receiptTotal"></span> €</strong></p>
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

    // Gestion des ventes multiples
    document.getElementById('addRow').addEventListener('click', function () {
        const newRow = document.createElement('tr');
        newRow.innerHTML = `
            <td>
                <select class="productSelect" name="product[]" required>
                    <?php
                    $sql = "SELECT id, nom, prix FROM Produits";
                    $stmt = $conn->query($sql);
                    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                        echo "<option value='{$row['id']}' data-prix='{$row['prix']}'>{$row['nom']}</option>";
                    }
                    ?>
                </select>
            </td>
            <td><input type="number" class="quantity" name="quantity[]" min="1" required></td>
            <td><input type="number" class="unitPrice" name="unitPrice[]" readonly></td>
            <td><input type="number" class="total" name="total[]" readonly></td>
            <td><button type="button" class="removeRow">Supprimer</button></td>
        `;
        document.querySelector('#salesTable tbody').appendChild(newRow);
    });

    document.addEventListener('change', function (e) {
        if (e.target.classList.contains('productSelect') || e.target.classList.contains('quantity')) {
            const row = e.target.closest('tr');
            const productSelect = row.querySelector('.productSelect');
            const quantityInput = row.querySelector('.quantity');
            const unitPriceInput = row.querySelector('.unitPrice');
            const totalInput = row.querySelector('.total');

            const prix = productSelect.options[productSelect.selectedIndex].getAttribute('data-prix');
            const quantite = quantityInput.value;

            unitPriceInput.value = prix;
            totalInput.value = (prix * quantite).toFixed(2);
        }
    });

    document.addEventListener('click', function (e) {
        if (e.target.classList.contains('removeRow')) {
            e.target.closest('tr').remove();
        }
    });

    document.getElementById('multiSalesForm').addEventListener('submit', function (e) {
        e.preventDefault();
        const formData = new FormData(this);

        fetch('save_multiple_sales.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showNotification('success', data.message);
                window.location.reload();
            } else {
                showNotification('error', data.message);
            }
        });
    });

    // Filtrage des ventes
    document.getElementById('applyFilters').addEventListener('click', function () {
        const filterDate = document.getElementById('filterDate').value;
        const filterProduct = document.getElementById('filterProduct').value;

        fetch(`filter_sales.php?date=${filterDate}&product=${filterProduct}`)
            .then(response => response.json())
            .then(data => {
                const tbody = document.querySelector('#salesSummary tbody');
                tbody.innerHTML = '';

                data.forEach(sale => {
                    const row = document.createElement('tr');
                    row.innerHTML = `
                        <td>${sale.date_vente}</td>
                        <td>${sale.produit_nom}</td>
                        <td>${sale.quantite}</td>
                        <td>${sale.montant_total}</td>
                    `;
                    tbody.appendChild(row);
                });
            });
    });

    // Impression du reçu
    document.getElementById('printReceipt').addEventListener('click', async function () {
    // Récupérer les ventes sélectionnées
    const selectedSales = [];
    document.querySelectorAll('.saleCheckbox:checked').forEach(checkbox => {
        selectedSales.push(checkbox.getAttribute('data-id'));
    });

    // Vérifier si au moins une vente est sélectionnée
    if (selectedSales.length === 0) {
        alert('Veuillez sélectionner au moins une vente pour imprimer le reçu.');
        return;
    }

    // Récupérer les données des ventes sélectionnées depuis l'API
    const response = await fetch('print_receipt.php?selectedSales=' + selectedSales.join(','));
    const ventes = await response.json();

    // Calculer le total général
    const totalGeneral = ventes.reduce((acc, vente) => acc + parseFloat(vente.montant_total), 0);

    // Récupérer le mode de paiement (à adapter selon votre logique)
    const paymentMethod = "Espèces"; // Exemple, à remplacer par la valeur réelle

    // Ouvrir une nouvelle fenêtre pour l'impression
    const printWindow = window.open('', '', 'height=600,width=800');
    printWindow.document.write('<html><head><title>Reçu de vente</title><style>');
    printWindow.document.write(`
        body { font-family: Arial, sans-serif; margin: 0; padding: 20px; }
        h1 { text-align: center; font-size: 24px; color: #333; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f5f5f5; color: #333; }
        .total { text-align: right; font-size: 16px; font-weight: bold; color: #333; }
        .footer { text-align: center; margin-top: 20px; font-size: 12px; color: #777; }
    `);
    printWindow.document.write('</style></head><body>');
    printWindow.document.write('<h1>Reçu de vente</h1>');
    printWindow.document.write('<p>Nom de l\'entreprise : <strong>Jacques ERP</strong></p>');
    printWindow.document.write(`<p>Date : ${new Date().toLocaleString()}</p>`);
    printWindow.document.write(`<p>Mode de paiement : <strong>${paymentMethod}</strong></p>`);
    printWindow.document.write('<table>');
    printWindow.document.write('<thead><tr><th>Produit</th><th>Quantité</th><th>Prix unitaire</th><th>Total</th></tr></thead>');
    printWindow.document.write('<tbody>');

    // Ajouter les données des ventes sélectionnées
    ventes.forEach(vente => {
        const prixUnitaire = (vente.montant_total / vente.quantite).toFixed(2);
        printWindow.document.write(`
            <tr>
                <td>${vente.nom}</td>
                <td>${vente.quantite}</td>
                <td>${prixUnitaire} $</td>
                <td>${vente.montant_total} $</td>
            </tr>
        `);
    });

    printWindow.document.write('</tbody></table>');
    printWindow.document.write(`<div class="total"><p>Total : <strong>${totalGeneral.toFixed(2)} $</strong></p></div>`);
    printWindow.document.write('<div class="footer"><p>Merci pour votre achat !</p><p>Pour toute réclamation, contactez-nous au +243 819 665 262.</p></div>');
    printWindow.document.write('</body></html>');
    printWindow.document.close();
    printWindow.print();
});
    </script>
</body>
</html>
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
        </div>
    </div>

    <!-- Contenu principal -->
    <div class="container">
        <h1>Gestion des ventes</h1>

        <!-- Formulaire d'ajout/modification -->
        <form action="ventes.php" method="post" id="saleForm">
            <input type="hidden" id="saleId" name="saleId">
            <label for="date_vente">Date de vente :</label>
            <input type="date" id="date_vente" name="date_vente" required><br>
            <label for="produit_id">Produit :</label>
            <select id="produit_id" name="produit_id" required>
                <?php
                $sql = "SELECT id, nom, prix FROM Produits";
                $stmt = $conn->query($sql);
                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    echo "<option value='{$row['id']}' data-prix='{$row['prix']}'>{$row['nom']}</option>";
                }
                ?>
            </select><br>
            <label for="quantite">Quantité :</label>
            <input type="number" id="quantite" name="quantite" required><br>
            <label for="montant_total">Montant total :</label>
            <input type="number" id="montant_total" name="montant_total" step="0.01" readonly><br>
            <button type="submit" name="ajouter" id="submitBtn">Ajouter</button>
            <button type="button" id="cancelBtn" style="display: none;">Annuler</button>
        </form>

        <!-- Tableau des ventes -->
        <h2>Liste des ventes</h2>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Date</th>
                    <th>Produit</th>
                    <th>Quantité</th>
                    <th>Montant total</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $sql = "SELECT Ventes.*, Produits.nom AS produit_nom FROM Ventes JOIN Produits ON Ventes.produit_id = Produits.id";
                $stmt = $conn->query($sql);
                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    echo "<tr data-id='{$row['id']}'>
                            <td>{$row['id']}</td>
                            <td>{$row['date_vente']}</td>
                            <td>{$row['produit_nom']}</td>
                            <td>{$row['quantite']}</td>
                            <td>{$row['montant_total']}</td>
                            <td>
                                <button class='editBtn' data-id='{$row['id']}'><i class='fas fa-edit'></i></button>
                                <button class='deleteBtn' data-id='{$row['id']}'><i class='fas fa-trash'></i></button>
                            </td>
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

    // Calcul automatique du montant total
    document.getElementById('produit_id').addEventListener('change', function () {
        updateTotal();
    });

    document.getElementById('quantite').addEventListener('input', function () {
        updateTotal();
    });

    function updateTotal() {
        const produitId = document.getElementById('produit_id').value;
        const quantite = document.getElementById('quantite').value;
        const prix = document.querySelector(`#produit_id option[value='${produitId}']`).getAttribute('data-prix');
        const montantTotal = (prix * quantite).toFixed(2);
        document.getElementById('montant_total').value = montantTotal;
    }

    // Gestion de la modification
    document.querySelectorAll('.editBtn').forEach(button => {
        button.addEventListener('click', function () {
            const row = this.closest('tr');
            document.getElementById('saleId').value = row.getAttribute('data-id');
            document.getElementById('date_vente').value = row.cells[1].textContent;
            document.getElementById('produit_id').value = row.cells[2].textContent;
            document.getElementById('quantite').value = row.cells[3].textContent;
            document.getElementById('montant_total').value = row.cells[4].textContent;

            document.getElementById('submitBtn').textContent = 'Modifier';
            document.getElementById('cancelBtn').style.display = 'inline-block';
        });
    });

    // Gestion de l'annulation
    document.getElementById('cancelBtn').addEventListener('click', function () {
        document.getElementById('saleForm').reset();
        document.getElementById('submitBtn').textContent = 'Ajouter';
        this.style.display = 'none';
    });

    // Gestion de la suppression
    document.querySelectorAll('.deleteBtn').forEach(button => {
        button.addEventListener('click', function () {
            const saleId = this.getAttribute('data-id');
            Swal.fire({
                title: 'Êtes-vous sûr ?',
                text: 'Vous ne pourrez pas revenir en arrière !',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#1abc9c',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Oui, supprimer !'
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch(`delete_sale.php?id=${saleId}`)
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                showNotification('success', 'Vente supprimée avec succès !').then(() => {
                                    this.closest('tr').remove(); // Supprimer la ligne du tableau
                                });
                            } else {
                                showNotification('error', 'Erreur lors de la suppression de la vente.');
                            }
                        });
                }
            });
        });
    });

    // Soumission du formulaire
    document.getElementById('saleForm').addEventListener('submit', function (e) {
        e.preventDefault(); // Empêcher le rechargement de la page
        const formData = new FormData(this);

        fetch('save_sale.php', {
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
<?php include 'db.php'; ?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des produits</title>
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
        <h1>Gestion des produits</h1>

        <!-- Formulaire d'ajout/modification -->
        <form action="produits.php" method="post" id="productForm">
            <input type="hidden" id="productId" name="productId">
            <label for="nom">Nom du produit :</label>
            <input type="text" id="nom" name="nom" required><br>
            <label for="description">Description :</label>
            <textarea id="description" name="description"></textarea><br>
            <label for="prix">Prix :</label>
            <input type="number" id="prix" name="prix" step="0.01" required><br>
            <label for="quantite">Quantité en stock :</label>
            <input type="number" id="quantite" name="quantite" required><br>
            <label for="fournisseur_id">Fournisseur :</label>
            <select id="fournisseur_id" name="fournisseur_id">
                <?php
                $sql = "SELECT id, nom FROM Fournisseurs";
                $stmt = $conn->query($sql);
                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    echo "<option value='{$row['id']}'>{$row['nom']}</option>";
                }
                ?>
            </select><br>
            <button type="submit" name="ajouter" id="submitBtn">Ajouter</button>
            <button type="button" id="cancelBtn" style="display: none;">Annuler</button>
        </form>

        <!-- Tableau des produits -->
        <h2>Liste des produits</h2>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nom</th>
                    <th>Description</th>
                    <th>Prix</th>
                    <th>Quantité</th>
                    <th>Fournisseur</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $sql = "SELECT Produits.*, Fournisseurs.nom AS fournisseur_nom FROM Produits JOIN Fournisseurs ON Produits.fournisseur_id = Fournisseurs.id";
                $stmt = $conn->query($sql);
                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    echo "<tr data-id='{$row['id']}'>
                            <td>{$row['id']}</td>
                            <td>{$row['nom']}</td>
                            <td>{$row['description']}</td>
                            <td>{$row['prix']}</td>
                            <td>{$row['quantite_en_stock']}</td>
                            <td>{$row['fournisseur_nom']}</td>
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
    // Fonction pour afficher une notification
    function showNotification(type, message) {
        Swal.fire({
            icon: type, // 'success', 'error', 'warning', 'info'
            title: message,
            showConfirmButton: false,
            timer: 3000 // Fermer automatiquement après 3 secondes
        });
    }

    // Gestion de la modification
    document.querySelectorAll('.editBtn').forEach(button => {
        button.addEventListener('click', function () {
            const row = this.closest('tr');
            document.getElementById('productId').value = row.getAttribute('data-id');
            document.getElementById('nom').value = row.cells[1].textContent;
            document.getElementById('description').value = row.cells[2].textContent;
            document.getElementById('prix').value = row.cells[3].textContent;
            document.getElementById('quantite').value = row.cells[4].textContent;
            document.getElementById('fournisseur_id').value = row.cells[5].textContent;

            document.getElementById('submitBtn').textContent = 'Modifier';
            document.getElementById('cancelBtn').style.display = 'inline-block';
        });
    });

    // Gestion de l'annulation
    document.getElementById('cancelBtn').addEventListener('click', function () {
        document.getElementById('productForm').reset();
        document.getElementById('submitBtn').textContent = 'Ajouter';
        this.style.display = 'none';
    });

    // Gestion de la suppression
    document.querySelectorAll('.deleteBtn').forEach(button => {
        button.addEventListener('click', function () {
            const productId = this.getAttribute('data-id');
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
                    fetch(`delete_product.php?id=${productId}`)
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                showNotification('success', 'Produit supprimé avec succès !');
                                this.closest('tr').remove();
                            } else {
                                showNotification('error', 'Erreur lors de la suppression du produit.');
                            }
                        });
                }
            });
        });
    });

    // Soumission du formulaire
    document.getElementById('productForm').addEventListener('submit', function (e) {
        e.preventDefault(); // Empêcher le rechargement de la page
        const formData = new FormData(this);

        fetch('save_product.php', {
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

    // Fonction pour afficher une notification avec une promesse
    function showNotification(type, message) {
        return Swal.fire({
            icon: type, // 'success', 'error', 'warning', 'info'
            title: message,
            showConfirmButton: false,
            timer: 2000 // Fermer automatiquement après 2 secondes
        });
    };
    </script>
</body>
</html>
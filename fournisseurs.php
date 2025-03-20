<?php include 'db.php'; ?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des fournisseurs</title>
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
        <h1>Gestion des fournisseurs</h1>

        <!-- Formulaire d'ajout/modification -->
        <form action="fournisseurs.php" method="post" id="supplierForm">
            <input type="hidden" id="supplierId" name="supplierId">
            <label for="nom">Nom du fournisseur :</label>
            <input type="text" id="nom" name="nom" required><br>
            <label for="adresse">Adresse :</label>
            <textarea id="adresse" name="adresse"></textarea><br>
            <label for="telephone">Téléphone :</label>
            <input type="text" id="telephone" name="telephone" required><br>
            <button type="submit" name="ajouter" id="submitBtn">Ajouter</button>
            <button type="button" id="cancelBtn" style="display: none;">Annuler</button>
        </form>

        <!-- Tableau des fournisseurs -->
        <h2>Liste des fournisseurs</h2>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nom</th>
                    <th>Adresse</th>
                    <th>Téléphone</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $sql = "SELECT * FROM Fournisseurs";
                $stmt = $conn->query($sql);
                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    echo "<tr data-id='{$row['id']}'>
                            <td>{$row['id']}</td>
                            <td>{$row['nom']}</td>
                            <td>{$row['adresse']}</td>
                            <td>{$row['telephone']}</td>
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

    // Gestion de la modification
    document.querySelectorAll('.editBtn').forEach(button => {
        button.addEventListener('click', function () {
            const row = this.closest('tr');
            document.getElementById('supplierId').value = row.getAttribute('data-id');
            document.getElementById('nom').value = row.cells[1].textContent;
            document.getElementById('adresse').value = row.cells[2].textContent;
            document.getElementById('telephone').value = row.cells[3].textContent;

            document.getElementById('submitBtn').textContent = 'Modifier';
            document.getElementById('cancelBtn').style.display = 'inline-block';
        });
    });

    // Gestion de l'annulation
    document.getElementById('cancelBtn').addEventListener('click', function () {
        document.getElementById('supplierForm').reset();
        document.getElementById('submitBtn').textContent = 'Ajouter';
        this.style.display = 'none';
    });

    // Gestion de la suppression
    document.querySelectorAll('.deleteBtn').forEach(button => {
        button.addEventListener('click', function () {
            const supplierId = this.getAttribute('data-id');
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
                    fetch(`delete_supplier.php?id=${supplierId}`)
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                showNotification('success', 'Fournisseur supprimé avec succès !').then(() => {
                                    this.closest('tr').remove(); // Supprimer la ligne du tableau
                                });
                            } else {
                                showNotification('error', 'Erreur lors de la suppression du fournisseur.');
                            }
                        });
                }
            });
        });
    });

    // Soumission du formulaire
    document.getElementById('supplierForm').addEventListener('submit', function (e) {
        e.preventDefault(); // Empêcher le rechargement de la page
        const formData = new FormData(this);

        fetch('save_supplier.php', {
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
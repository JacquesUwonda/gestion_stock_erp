<?php include 'db.php'; ?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des paiements</title>
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
        <h1>Gestion des paiements</h1>

        <!-- Formulaire d'enregistrement des paiements -->
        <form id="paymentForm">
            <label for="vente_id">Vente associée :</label>
            <select id="vente_id" name="vente_id" required>
                <?php
                $sql = "SELECT id, date_vente, montant_total FROM Ventes";
                $stmt = $conn->query($sql);
                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    echo "<option value='{$row['id']}'>{$row['date_vente']} - Montant: {$row['montant_total']} €</option>";
                }
                ?>
            </select><br>
            <label for="montant_paye">Montant payé :</label>
            <input type="number" id="montant_paye" name="montant_paye" step="0.01" required><br>
            <label for="mode_paiement">Mode de paiement :</label>
            <select id="mode_paiement" name="mode_paiement" required>
                <option value="espèces">Espèces</option>
                <option value="carte">Carte</option>
                <option value="virement">Virement</option>
            </select><br>
            <button type="submit">Enregistrer le paiement</button>
        </form>

        <!-- Tableau récapitulatif des paiements -->
        <h2>Suivi des paiements</h2>
        <div class="filters">
            <label for="filterDate">Date :</label>
            <input type="date" id="filterDate">
            <label for="filterVente">Vente :</label>
            <select id="filterVente">
                <option value="">Toutes les ventes</option>
                <?php
                $sql = "SELECT id, date_vente FROM Ventes";
                $stmt = $conn->query($sql);
                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    echo "<option value='{$row['id']}'>{$row['date_vente']}</option>";
                }
                ?>
            </select>
            <button id="applyFilters">Appliquer</button>
        </div>

        <table id="paymentsSummary">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Vente associée</th>
                    <th>Montant payé</th>
                    <th>Mode de paiement</th>
                    <th>Solde restant</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $sql = "SELECT Paiements.*, Ventes.date_vente, Ventes.montant_total FROM Paiements JOIN Ventes ON Paiements.vente_id = Ventes.id";
                $stmt = $conn->query($sql);
                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    $solde_restant = $row['montant_total'] - $row['montant_paye'];
                    echo "<tr>
                            <td>{$row['date_paiement']}</td>
                            <td>{$row['date_vente']}</td>
                            <td>{$row['montant_paye']} €</td>
                            <td>{$row['mode_paiement']}</td>
                            <td>{$solde_restant} €</td>
                            <td>
                                <button class='deletePaymentBtn' data-id='{$row['id']}'><i class='fas fa-trash'></i></button>
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
        return Swal.fire({
            icon: type, // 'success', 'error', 'warning', 'info'
            title: message,
            showConfirmButton: false,
            timer: 2000 // Fermer automatiquement après 2 secondes
        });
    }

    // Soumission du formulaire de paiement
    document.getElementById('paymentForm').addEventListener('submit', function (e) {
        e.preventDefault();
        const formData = new FormData(this);

        fetch('save_payment.php', {
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

    // Filtrage des paiements
    document.getElementById('applyFilters').addEventListener('click', function () {
        const filterDate = document.getElementById('filterDate').value;
        const filterVente = document.getElementById('filterVente').value;

        fetch(`filter_payments.php?date=${filterDate}&vente=${filterVente}`)
            .then(response => response.json())
            .then(data => {
                const tbody = document.querySelector('#paymentsSummary tbody');
                tbody.innerHTML = '';

                data.forEach(payment => {
                    const solde_restant = payment.montant_total - payment.montant_paye;
                    const row = document.createElement('tr');
                    row.innerHTML = `
                        <td>${payment.date_paiement}</td>
                        <td>${payment.date_vente}</td>
                        <td>${payment.montant_paye} €</td>
                        <td>${payment.mode_paiement}</td>
                        <td>${solde_restant} €</td>
                        <td>
                            <button class='deletePaymentBtn' data-id='${payment.id}'><i class='fas fa-trash'></i></button>
                        </td>
                    `;
                    tbody.appendChild(row);
                });
            });
    });

    // Suppression d'un paiement
    document.querySelectorAll('.deletePaymentBtn').forEach(button => {
        button.addEventListener('click', function () {
            const paymentId = this.getAttribute('data-id');
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
                    fetch(`delete_payment.php?id=${paymentId}`)
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                showNotification('success', 'Paiement supprimé avec succès !').then(() => {
                                    this.closest('tr').remove(); // Supprimer la ligne du tableau
                                });
                            } else {
                                showNotification('error', 'Erreur lors de la suppression du paiement.');
                            }
                        });
                }
            });
        });
    });
    </script>
</body>
</html>
<?php include 'db.php'; ?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion de Stock</title>
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

    <!-- Bannière d'accueil -->
    <div class="banner" >
        <div class="banner-content">
            <h1>Bienvenue chez JACQUES' ERP VENTES, Votre partenaire de confiance.</h1>
            <p>Gérez vos produits, fournisseurs, ventes et stocks en toute simplicité.</p>
            <a href="#features" class="btn">Découvrir les fonctionnalités</a>
        </div>
    </div>

    <!-- Section des fonctionnalités -->
    <div class="features" id="features">
        <h2>Fonctionnalités principales</h2>
        <div class="feature-cards">
            <div class="card">
                <i class="fas fa-box-open"></i>
                <h3>Gestion des produits</h3>
                <p>Ajoutez, modifiez et supprimez des produits facilement.</p>
                <a href="produits.php" class="btn">Accéder</a>
            </div>
            <div class="card">
                <i class="fas fa-truck"></i>
                <h3>Gestion des fournisseurs</h3>
                <p>Suivez vos fournisseurs et leurs informations.</p>
                <a href="fournisseurs.php" class="btn">Accéder</a>
            </div>
            <div class="card">
                <i class="fas fa-shopping-cart"></i>
                <h3>Gestion des ventes</h3>
                <p>Enregistrez et suivez vos ventes en temps réel.</p>
                <a href="ventes.php" class="btn">Accéder</a>
            </div>
            <div class="card">
                <i class="fas fa-warehouse"></i>
                <h3>Gestion des stocks</h3>
                <p>Contrôlez vos niveaux de stock et recevez des alertes.</p>
                <a href="stocks.php" class="btn">Accéder</a>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <div class="footer">
        <p>&copy; 2025 JACQUES'ERP VENTES. Tous droits réservés.</p>
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
    </script>
</body>
</html>
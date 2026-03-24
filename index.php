<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Johnny Barber</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>

<body>

    <!-- ===== HEADER ===== -->
    <header class="header">
        <div class="logo-container">
            <img src="./images/Logo_footer.png" alt="Logo Johnny Barber" class="logo">
        </div>
        <h1>Salon de coiffure</h1>

        <div class="header-actions">
            <a href="./Front-end/Formulaire.html" class="btn-reserver" id="reserv">Réserver</a>
            <a href="./Back-end/admin_login.php" class="btn-admin" id="admine" title="Espace administrateur"><i
                    class='fa-solid fa-circle-user'></i></a>
        </div>

    </header>

    <!-- ===== NAVIGATION ===== -->
    <nav class="nav">
        <a href="index.html" class="active">Accueil</a>
        <a href="./Front-end/Salon.html">Salon</a>
        <a href="./Front-end/services.html">Services</a>
        <a href="./Front-end/Formulaire.html">Réservation</a>
    </nav>

    <!-- ===== HERO ===== -->
    <section id="accueil" class="hero">
        <div class="overlay"></div>

        <div class="hero-content">
            <p class="hero-eyebrow">Paris · Depuis 2016</p>
            <h2 class="hero-titre">Johnny Barber</h2>
            <p class="hero-description">
                Bienvenue dans un espace où tradition et modernité se rencontrent.<br>
                Coupes hommes, barbes, colorations — chaque prestation est réalisée<br>
                avec soin, précision et passion par nos artisans coiffeurs.
            </p>
            <p class="hero-slogan">« Votre style, notre signature. »</p>
        </div>

        <div class="reserver-maintenant">
            <a href="./Front-end/Formulaire.html" class="btn-reserver">Réserver maintenant</a>
        </div>

    </section>

    <!-- ===== STATS ===== -->
    <section class="stats">
        <div class="stat">
            <h2>+5 000</h2>
            <p>Clients satisfaits</p>
        </div>
        <div class="stat">
            <h2>8</h2>
            <p>Années d'expérience</p>
        </div>
        <div class="stat">
            <h2>4.5 ★</h2>
            <p>Note moyenne</p>
        </div>
        <div class="stat">
            <h2>+10 000</h2>
            <p>Coupes réalisées</p>
        </div>
    </section>

    <!-- ===== SERVICES ===== -->
    <section id="services" class="services">

        <p class="subtitle">Ce que nous proposons</p>
        <h2>Excellence dans chaque détail</h2>
        <p class="description">
            Nous offrons une expérience premium avec les meilleurs professionnels
            et produits du marché, pour sublimer votre style à chaque visite.
        </p>

        <div class="cards">

            <div class="card">
                <img src="./images/coupe homme.jpg" alt="Coupe">
                <div class="card-body">
                    <h3>Coiffure Simple</h3>
                    <p><span>25 €</span>· 25 min</p>
                </div>
            </div>

            <div class="card">
                <img src="./images/barbe.webp" alt="Barbe">
                <div class="card-body">
                    <h3>Barbe (ça va barber)</h3>
                    <p><span>15 €</span>· 25 min</p>
                </div>
            </div>

            <div class="card">
                <img src="./images/coloration.jpg" alt="Coloration">
                <div class="card-body">
                    <h3>Coloration</h3>
                    <p><span>23 €</span>· 45 min</p>
                </div>
            </div>

            <div class="card">
                <img src="./images/produits.jpg" alt="Produits">
                <div class="card-body">
                    <h3>Produits</h3>
                    <p><span>23 €</span>· ...</p>
                </div>
            </div>

        </div>

        <div class="services-cta">
            <a href="./Front-end/services.html" class="btn-all">Voir tous nos services &amp; produits</a>
        </div>

    </section>

    <!-- ===== FOOTER ===== -->
    <footer class="footer">
        <div class="footer-container">

            <div class="footer-section">
                <img src="./images/Logo.png" alt="Logo Johnny Barber" class="logo">
                <h4>Johnny Barber</h4>
                <p>Excellence dans chaque détail.<br>Votre style, notre passion.</p>
            </div>

            <div class="footer-section">
                <h4>Contact</h4>
                <p>contact@johnnybarber.com</p>
                <p>06 12 34 56 78</p>
                <p>12 rue de la Paix, 75002 Paris</p>
            </div>

            <div class="footer-section">
                <h4>Navigation</h4>
                <p><a href="index.html">Accueil</a></p>
                <p><a href="./Front-end/Salon.html">Notre salon</a></p>
                <p><a href="./Front-end/services.html">Nos services</a></p>
                <p><a href="./Front-end/Formulaire.html">Réservation</a></p>
                <p><a href="./Front-end/mentions_legales.html">Mentions légales</a></p>
                <p><a href="./Front-end/politique_confidentialite.html">Politique de confidentialité</a></p>
            </div>

        </div>

        <div class="footer-bottom">
            <p>© 2026 Johnny Barber — Tous droits réservés</p>
        </div>
    </footer>
    <script src="script.js"></script>
</body>

</html>

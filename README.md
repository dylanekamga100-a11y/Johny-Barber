# ✂️ Johnny Barber

> Site vitrine et système de réservation en ligne pour le salon de coiffure **Johnny Barber** — Paris, depuis 2016.

---

## 📸 Aperçu

Interface élégante noir & or, pensée pour refléter l'identité premium du salon.  
Côté client : navigation fluide, réservation en ligne, découverte des services.  
Côté admin : espace sécurisé de gestion des rendez-vous et des prestations.

---

## 🗂️ Structure du projet
```
Jonhy-Barber/
├── index.html                          # Page d'accueil
├── styles.css                          # Feuille de style globale
├── script.js                           # Scripts JS
├── images/                             # Assets visuels
├── Front-end/
│   ├── Formulaire.html                 # Formulaire de réservation
│   ├── Salon.html                      # Présentation du salon
│   ├── services.html                   # Catalogue des services
│   ├── mentions_legales.html           # Mentions légales
│   └── politique_confidentialite.html  # Politique de confidentialité
└── Back-end/
    ├── config.php                      # Connexion PDO à la BDD
    ├── admin_login.php                 # Authentification administrateur
    ├── admin_dashboard.php             # Tableau de bord admin
    ├── logout.php                      # Déconnexion
    ├── traitement_reservation.php      # Traitement du formulaire client
    ├── update_rdv.php                  # Mise à jour du statut d'un RDV
    └── save_service.php                # Ajout / suppression de services
```

---

## ⚙️ Stack technique

| Couche | Technologies |
|---|---|
| Frontend | HTML5, CSS3, JavaScript |
| Backend | PHP 8+ |
| Base de données | MySQL (PDO) |
| Icônes | Font Awesome 6.5 |
| Typographie | Cormorant Garamond, Montserrat (Google Fonts) |
| Environnement local | Laragon |

---

## 🚀 Installation

### Prérequis

- [Laragon](https://laragon.org/) (ou XAMPP/WAMP)
- PHP 8.0+
- MySQL 5.7+

### Étapes

**1. Cloner le dépôt**
```bash
git clone https://github.com/votre-utilisateur/Jonhy-Barber.git
```

**2. Placer le projet dans le dossier Laragon**
```
C:/laragon/www/php/Jonhy-Barber/
```

**3. Créer la base de données**

Ouvrir phpMyAdmin et exécuter le script suivant :
```sql
CREATE DATABASE IF NOT EXISTS johny_barber
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE johny_barber;

CREATE TABLE IF NOT EXISTS admins (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS services (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,
    prix DECIMAL(6,2) NOT NULL,
    duree INT NOT NULL,
    photo VARCHAR(255),
    actif TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS reservations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL,
    telephone VARCHAR(20) NOT NULL,
    service_id INT NOT NULL,
    date DATE NOT NULL,
    heure TIME NOT NULL,
    message TEXT,
    statut ENUM('attente', 'accepte', 'refuse') DEFAULT 'attente',
    traite_par INT DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (service_id) REFERENCES services(id) ON DELETE RESTRICT,
    FOREIGN KEY (traite_par) REFERENCES admins(id) ON DELETE SET NULL
);

INSERT INTO admins (nom, email, password) VALUES (
    'Johnny Barber',
    'johnybarber@gmail.com',
    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'
);

INSERT INTO services (nom, prix, duree, photo) VALUES
    ('Coupe homme',  25.00, 60, 'coupe homme.jpg'),
    ('Coupe femme',  25.00, 60, 'coupe-femme.webp'),
    ('Barbe',        15.00, 25, 'barbe.webp'),
    ('Coloration',   23.00, 45, 'coloration.jpg');
```

> ⚠️ Le hash inséré correspond au mot de passe `admin123`.  
> Pour générer votre propre hash, créez un fichier temporaire :
> ```php
> <?php echo password_hash('votre_mot_de_passe', PASSWORD_BCRYPT); ?>
> ```
> Puis mettez à jour la table `admins` avec le hash généré.

**4. Vérifier la configuration BDD**

Dans `Back-end/config.php`, vérifiez que les paramètres correspondent à votre environnement :
```php
$pdo = new PDO(
    "mysql:host=localhost;dbname=johny_barber;charset=utf8mb4",
    "root",   // votre utilisateur MySQL
    "",       // votre mot de passe MySQL
    [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
);
```

**5. Accéder au projet**
```
http://localhost/php/Jonhy-Barber/index.html
```

---

## 🔐 Espace administrateur

Accessible via l'icône profil dans le header de toutes les pages.
```
URL    : http://localhost/php/Jonhy-Barber/Back-end/admin_login.php
Email  : johnybarber@gmail.com
Mdp    : admin123
```

### Fonctionnalités du dashboard

- 📋 Visualisation de toutes les réservations en temps réel
- ✅ Accepter ou refuser un rendez-vous
- ✂️ Ajouter un nouveau service (nom, prix, durée, photo)
- 🗑️ Supprimer un service existant
- 🔒 Session sécurisée avec déconnexion

---

## 📋 Fonctionnalités

### Côté client
- Présentation du salon, de l'équipe et des services
- Formulaire de réservation avec validation côté serveur
- Email de confirmation envoyé automatiquement au client
- Email de notification envoyé au salon
- Pages légales (mentions légales, politique de confidentialité)

### Côté admin
- Authentification sécurisée (bcrypt + sessions PHP)
- Gestion complète des rendez-vous (statuts : en attente / accepté / refusé)
- Gestion des services avec upload de photo
- Retour au site sans déconnexion

---

## 👥 Équipe

Projet réalisé en groupe dans le cadre d'un projet scolaire.

---

## 📄 Licence

Ce projet est à usage éducatif. Tous droits réservés © 2026 Johnny Barber.

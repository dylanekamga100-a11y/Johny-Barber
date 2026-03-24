# Johnny Barber — Site web & Système de réservation

> Salon de coiffure parisien fondé en 2016. Site vitrine avec prise de rendez-vous en ligne et interface d'administration complète.

---

## Sommaire

1. [Présentation](#présentation)
2. [Stack technique](#stack-technique)
3. [Structure du projet](#structure-du-projet)
4. [Installation](#installation)
5. [Base de données](#base-de-données)
6. [Fonctionnalités](#fonctionnalités)
7. [Pages & fichiers](#pages--fichiers)
8. [Charte graphique](#charte-graphique)
9. [Sécurité](#sécurité)
10. [À venir / Améliorations possibles](#à-venir--améliorations-possibles)

---

## Présentation

**Johnny Barber** est un site web complet pour un salon de coiffure situé au 12 rue de la Paix, 75002 Paris. Il comprend :

- Un **site vitrine** présentant le salon, les services et les tarifs
- Un **formulaire de réservation** en ligne avec validation double (client + serveur) et notifications par email
- Un **dashboard administrateur** sécurisé par session PHP pour gérer les rendez-vous et les services

---

## Stack technique

| Couche | Technologie |
|---|---|
| Frontend | HTML5, CSS3 (custom), JavaScript (vanilla) |
| Backend | PHP 8+ |
| Base de données | MySQL 8+ (via PDO) |
| Envoi d'emails | `mail()` PHP natif + PHPMailer (bibliothèque incluse) |
| Serveur local | Laragon (Apache + MySQL) |
| Icônes | Font Awesome 6.5.0 (CDN) |
| Polices | Cormorant Garamond + Montserrat (Google Fonts) |

---

## Structure du projet

```
Jonhy-Barber/
│
├── index.html                            # Page d'accueil
├── styles.css                            # Feuille de styles globale (toutes les pages)
├── script.js                             # JS global : scroll header, animations reveal, nav active ...
│
├── images/                               # Médias du site
│   ├── White_Barber_Logo.png             # Logo blanc
│   ├── k2bxjslly5nboqwbamap.webp        # Image de fond (hero)
│   ├── coupe homme.jpg
│   ├── barbe.webp
│   ├── coloration.jpg
│   ├── produits.jpg
│   └── Untitled_design_1.jpg
│
├── Front-end/
│   ├── Formulaire.html                   # Formulaire de réservation
│   ├── Salon.html                        # Présentation du salon + carte Google Maps
│   ├── services.html                     # Catalogue complet des prestations & produits
│   ├── mentions_legales.html             # Mentions légales
│   └── politique_confidentialite.html    # Politique de confidentialité (RGPD)
│
└── Back-end/
   ├── config.php                        # Connexion PDO (BDD : johny_barber)
   ├── admin_login.php                   # Page + traitement de la connexion admin
   ├── admin_dashboard.php               # Dashboard : gestion RDV + services
   ├── logout.php                        # Destruction de session + redirection
   ├── traitement_reservation.php        # Validation + insertion d'une réservation
   ├── update_rdv.php                    # Mise à jour du statut d'un RDV
   ├── save_service.php                  # Ajout / suppression d'un service + upload photo
   └── hash.php                          # Utilitaire : génère un hash bcrypt (usage unique)

```

---

## Installation

### Prérequis

- [Laragon](https://laragon.org/) (ou XAMPP / WAMP)
- PHP 8.0+
- MySQL 8.0+

### Étapes

1. **Placer le projet** dans le dossier `www` de Laragon :
   ```
   C:/laragon/www/Jonhy-Barber/
   ```

2. **Créer la base de données** dans phpMyAdmin :
   ```sql
   CREATE DATABASE johny_barber CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
   ```

3. **Importer les tables** (voir section [Base de données](#base-de-données))
   

4. **Vérifier `config.php`** si nécessaire :
   ```php
   $pdo = new PDO(
       "mysql:host=localhost;dbname=johny_barber;charset=utf8mb4",
       "root",  // utilisateur MySQL
       "",      // mot de passe (vide par défaut sur Laragon)
   );
   ```

5. **Accéder au site** :
   ```
   http://localhost/Jonhy-Barber/index.html
   ```

---

## Base de données

### Nom : `johny_barber`

### Table `admins`

Stocke les comptes administrateurs. La connexion se fait par **email + mot de passe**.

```sql
CREATE TABLE admins (
    id         INT AUTO_INCREMENT PRIMARY KEY,
    email      VARCHAR(150) NOT NULL UNIQUE,
    password   VARCHAR(255) NOT NULL,
    nom        VARCHAR(100) NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

---

### Table `services`

Gérée dynamiquement depuis le dashboard (ajout, suppression, upload photo).

```sql
CREATE TABLE services (
    id         INT AUTO_INCREMENT PRIMARY KEY,
    nom        VARCHAR(100) NOT NULL,
    prix       DECIMAL(6,2) NOT NULL,         -- ex: 25.00
    duree      INT NOT NULL,                  -- durée en minutes
    photo      VARCHAR(255) NULL,             -- nom du fichier stocké dans /images/
    actif      TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

---

### Table `reservations`

Alimentée par le formulaire de réservation public. Le statut est géré depuis le dashboard.

```sql
CREATE TABLE reservations (
    id         INT AUTO_INCREMENT PRIMARY KEY,
    nom        VARCHAR(100) NOT NULL,
    email      VARCHAR(150) NOT NULL,
    telephone  VARCHAR(20)  NOT NULL,
    service_id INT NULL,
    date       DATE NOT NULL,
    heure      TIME NOT NULL,
    message    TEXT NULL,
    statut     ENUM('attente', 'accepte', 'refuse') DEFAULT 'attente',
    traite_par INT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (service_id) REFERENCES services(id) ON DELETE SET NULL
);
```

---

### Données de base recommandées

```sql
INSERT INTO admins (nom, email, password) VALUES (
    'Johnny Barber',
    'johnybarber@gmail.com',
    '$2y$10$MyuDizIss1fz5btooT/GJueerHN4AF4Efj22fYPNBQM74Yire/gNe'

INSERT INTO services (nom, prix, duree) VALUES
    ('Coupe homme',  25.00, 60),
    ('Produits',     13.00, 13),
    ('Barbe',        15.00, 25),
    ('Coloration',   23.00, 45);
```

---

## Fonctionnalités

### Site vitrine (`index.html`)

- Hero plein écran avec image de fond fixe, slogan et bouton de réservation
- Section stats : +5 000 clients, 8 ans d'expérience, note 4.5★, 10 000 coupes
- Aperçu des 4 services principaux avec photos et tarifs
- Header dynamique au scroll (`script.js`) : fond et ombre s'activent après 50px
- Navigation active détectée automatiquement par l'URL courante

### Page Salon (`Salon.html`)

- Présentation du lieu avec texte d'introduction
- 3 blocs d'infos pratiques : adresse, contact, horaires (Lun-Ven 9h-19h / Sam 10h-18h / Dim fermé)
- Carte Google Maps intégrée (iframe) — 12 rue de la Paix, 75002 Paris

### Page Services (`services.html`)

- Catalogue complet organisé par catégories : Coiffures, Barbe, Couleur & Soin, Produits
- Chaque prestation affiche : nom, sous-titre, description, prix et durée
- Bouton "Réserver" sur chaque prestation renvoyant vers `Formulaire.html`
- Animation d'apparition au scroll via `IntersectionObserver` (`script.js`)

### Formulaire de réservation (`Formulaire.html`)

- Champs : nom complet, email, téléphone, service (select), date, heure, message (optionnel)
- **Validation côté client** : champs requis HTML5, date minimum = aujourd'hui (JS)
- **Validation côté serveur** (`traitement_reservation.php`) : format email, date dans le passé, champs vides
- **Enregistrement en BDD** : `service_id` résolu via requête `SELECT` sur la table `services`
- **Emails automatiques** via `mail()` : notification au salon + confirmation au client
- **Bannière de retour** : succès ou liste d'erreurs lus depuis les paramètres `?statut=` et `?msg=`
- Formulaire désactivé visuellement après une réservation réussie

### Dashboard administrateur (`admin_dashboard.php`)

Accessible via `Back-end/admin_login.php`. Authentification par **email + mot de passe** (bcrypt).

#### Connexion (`admin_login.php`)
- Formulaire avec icônes Font Awesome dans les champs
- Toggle affichage / masquage du mot de passe
- Lien retour vers le site public
- Message d'erreur si identifiants invalides (`?error=1`)

#### Gestion des rendez-vous
- Tableau chargé depuis la BDD avec `LEFT JOIN` sur `services` pour afficher le nom de la prestation
- Dates formatées `dd/mm/YYYY`, heures tronquées à `HH:MM`
- Compteurs en temps réel : en attente / acceptés / refusés
- **Filtres par statut** (JavaScript, sans rechargement de page)
- **Accepter / Refuser** via formulaires POST vers `update_rdv.php`
- **Remettre en attente** pour les RDV déjà traités
- Messages flash après chaque action, URL nettoyée automatiquement

#### Gestion des services
- Grille de cartes chargée dynamiquement depuis la BDD
- Affichage de la photo du service, ou icône placeholder si absente
- Prix formaté `25,00 €`, durée en `60 min`
- **Formulaire d'ajout** : nom, prix (décimal), durée (entier), photo (optionnelle)
- **Upload photo** : extensions autorisées (jpg, jpeg, png, webp), nom unique via `uniqid()`
- **Suppression** avec `confirm()` côté navigateur + DELETE en BDD

#### Navigation sidebar
- Bouton **Accueil** : ouvre `index.html` dans un nouvel onglet (session préservée)
- Bouton **Déconnexion** : `session_destroy()` + redirection vers `index.html`

---

## Pages & fichiers

### Frontend

| Fichier | Contenu |
|---|---|
| `index.html` | Accueil : hero, stats, aperçu services, footer |
| `Front-end/Salon.html` | Salon : présentation, horaires, adresse, Google Maps |
| `Front-end/services.html` | Catalogue complet : coiffures, barbe, couleur, produits |
| `Front-end/Formulaire.html` | Réservation en ligne avec retour PHP |
| `Front-end/mentions_legales.html` | Mentions légales |
| `Front-end/politique_confidentialite.html` | Politique de confidentialité (RGPD) |
| `styles.css` | Styles globaux : variables CSS, header, nav, hero, cards, formulaire, dashboard |
| `script.js` | Header au scroll, animations reveal, détection nav active |

### Backend

| Fichier | Rôle |
|---|---|
| `config.php` | Connexion PDO à `johny_barber` sur `localhost` |
| `admin_login.php` | Formulaire + authentification par email/password |
| `admin_dashboard.php` | Interface admin complète : RDV + services |
| `logout.php` | `session_destroy()` + redirection vers `index.html` |
| `traitement_reservation.php` | Validation, insertion BDD, envoi emails |
| `update_rdv.php` | Met à jour le champ `statut` d'une réservation |
| `save_service.php` | Ajoute ou supprime un service, gère l'upload photo |
| `hash.php` | Génère un hash bcrypt — **à supprimer après usage** |

---

## Charte graphique

| Élément | Valeur |
|---|---|
| Or (couleur principale) | `#FFCC00` |
| Noir (fond) | `#000000` |
| Blanc (texte) | `#F8F8F8` |
| Police titres | Cormorant Garamond (serif, italique) |
| Police corps | Montserrat (sans-serif, léger) |
| Icônes | Font Awesome 6.5.0 |

Variables CSS principales définies dans `styles.css` :

```css
--or:         #FFCC00;
--noir:       #000000;
--blanc:      #F8F8F8;
--grad-or:    linear-gradient(135deg, #FFCC00, #ffe14d, #b89900);
--font-titre: 'Cormorant Garamond', Georgia, serif;
--font-corps: 'Montserrat', sans-serif;
--transition: 0.35s cubic-bezier(0.4, 0, 0.2, 1);
--radius:     4px;
```

---

## Sécurité

| Point | Mesure appliquée |
|---|---|
| Injections SQL | Requêtes préparées PDO avec paramètres nommés sur toutes les requêtes |
| XSS | `htmlspecialchars()` sur toutes les données affichées |
| Authentification | `password_hash()` bcrypt + `password_verify()` |
| Protection pages admin | Vérification de `$_SESSION['admin_id']` à chaque chargement |
| Upload fichiers | Validation d'extension, nom régénéré avec `uniqid()` |
| Validation formulaire | Double couche : HTML5/JS côté client + PHP côté serveur |
| Fichier sensible | `hash.php` à supprimer après la création du compte admin |

---

## À venir / Améliorations possibles

- [ ] Brancher **PHPMailer** pour un envoi SMTP fiable en remplacement de `mail()`
- [ ] Badge de notifications dans la sidebar : nombre de RDV en attente
- [ ] Édition d'un service existant (modifier nom, prix, durée, photo)
- [ ] Pagination du tableau des réservations
- [ ] Filtrage et tri des réservations par date ou par service
- [ ] Export CSV des réservations
- [ ] Système de créneaux horaires avec gestion des disponibilités
- [ ] Page de confirmation dédiée après réservation (au lieu de la bannière)
- [ ] Email de rappel automatique 24h avant le rendez-vous
- [ ] Support multi-administrateurs avec niveaux d'accès

---

*© 2026 Johnny Barber — Tous droits réservés*
*12 rue de la Paix, 75002 Paris · contact@johnnybarber.com · 06 12 34 56 78*

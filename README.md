# Johnny Barber — Site web & Système de réservation

> Salon de coiffure parisien. Site vitrine avec formulaire de réservation en ligne et interface d'administration complète.

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

**Johnny Barber** est un site web complet pour un salon de coiffure parisien. Il comprend :

- Un **site vitrine** présentant le salon, les services et les tarifs
- Un **formulaire de réservation** en ligne avec validation et confirmation par email
- Un **dashboard administrateur** sécurisé pour gérer les rendez-vous et les services

---

## Stack technique

| Couche | Technologie |
|---|---|
| Frontend | HTML5, CSS3 (custom) |
| Backend | PHP 8+ |
| Base de données | MySQL 8+ (via PDO) |
| Serveur local | Laragon (Apache + MySQL) |
| Icônes | Font Awesome 6.5.0 |
| Polices | Cormorant Garamond, Montserrat (Google Fonts) |

---

## Structure du projet

```
Jonhy-Barber/
│
├── index.html                        # Page d'accueil
├── styles.css                        # Feuille de styles globale
│
├── images/                           # Médias (logo, photos services, fond)
│   ├── White_Barber_Logo.png
│   ├── coupe homme.jpg
│   ├── barbe.webp
│   ├── coloration.jpg
│   └── ...
│
├── Front-end/
│   ├── Formulaire.html               # Formulaire de réservation
│   ├── Salon.html                    # Page de présentation du salon
│   ├── services.html                 # Page des services & tarifs
│   ├── mentions_legales.html         # Mentions légales
│   └── politique_confidentialite.html
│
└── Back-end/
    ├── config.php                    # Connexion PDO à la base de données
    ├── admin_login.php               # Page de connexion administrateur
    ├── admin_dashboard.php           # Dashboard admin (RDV + services)
    ├── logout.php                    # Déconnexion (session_destroy)
    ├── traitement_reservation.php    # Traitement du formulaire de réservation
    ├── update_rdv.php                # Mise à jour du statut d'un RDV
    └── save_service.php              # Ajout / suppression d'un service
```

---

## Installation

### Prérequis

- [Laragon](https://laragon.org/) (ou XAMPP / WAMP)
- PHP 8.0+
- MySQL 8.0+

### Étapes

1. **Cloner ou copier** le projet dans le dossier `www` de Laragon :
   ```
   C:/laragon/www/Jonhy-Barber/
   ```

2. **Créer la base de données** dans phpMyAdmin :
   ```sql
   CREATE DATABASE johny_barber CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
   ```

3. **Importer la structure** (voir section [Base de données](#base-de-données) ci-dessous)

4. **Vérifier la configuration** dans `Back-end/config.php` :
   ```php
   $pdo = new PDO(
       "mysql:host=localhost;dbname=johny_barber;charset=utf8mb4",
       "root",  // utilisateur MySQL
       "",      // mot de passe (vide par défaut sur Laragon)
       [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
   );
   ```

5. **Accéder au site** via :
   ```
   http://localhost/Jonhy-Barber/index.html
   ```

---

## Base de données

### Nom de la base : `johny_barber`

### Table `admins`

```sql
CREATE TABLE admins (
    id         INT AUTO_INCREMENT PRIMARY KEY,
    email      VARCHAR(150) NOT NULL,
    password   VARCHAR(255) NOT NULL,  -- hash bcrypt
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

> Le mot de passe est hashé avec `password_hash()` (bcrypt). Pour créer un admin :
> ```php
> echo password_hash('votre_mot_de_passe', PASSWORD_DEFAULT);
> ```
> Puis insérer le hash en base.

---

### Table `services`

```sql
CREATE TABLE services (
    id         INT AUTO_INCREMENT PRIMARY KEY,
    nom        VARCHAR(100) NOT NULL,
    prix       DECIMAL(6,2) NOT NULL,
    duree      INT NOT NULL,              -- durée en minutes
    photo      VARCHAR(255) NULL,         -- nom du fichier dans /images/
    actif      TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

---

### Table `reservations`

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

### Données par défaut

```sql
-- Admin par défaut (mot de passe : admin123)
INSERT INTO admins (login, password)
VALUES ('admin', '$2y$10$VOTRE_HASH_BCRYPT_ICI');

-- Services de base
INSERT INTO services (nom, prix, duree) VALUES
    ('Coupe homme',  25.00, 60),
    ('Coupe femme',  35.00, 75),
    ('Barbe',        15.00, 25),
    ('Coloration',   23.00, 45);
```

---

## Fonctionnalités

### Site vitrine

- Page d'accueil avec hero, statistiques et aperçu des services
- Page salon avec informations pratiques et carte
- Page services avec grille des prestations et tarifs
- Navigation fixe avec animation dorée active

### Formulaire de réservation

- Champs : nom, email, téléphone, service, date, heure, message (optionnel)
- **Validation côté client** : champs requis, date minimum = aujourd'hui
- **Validation côté serveur** : format email, date passée, champs vides
- **Enregistrement en BDD** avec `JOIN` sur la table `services`
- **Emails automatiques** : confirmation au client + notification au salon
- **Bannière de retour** : message vert (succès) ou rouge (erreur) après soumission

### Dashboard administrateur

Accessible via `Back-end/admin_login.php` — authentification requise.

#### Gestion des rendez-vous
- Tableau complet des réservations chargé depuis la BDD
- JOIN sur `services` pour afficher le nom de la prestation
- Compteurs en temps réel : en attente / acceptés / refusés
- **Filtres** par statut (sans rechargement de page)
- Boutons **Accepter** / **Refuser** / **Remettre en attente**
- Messages flash après chaque action

#### Gestion des services
- Liste dynamique chargée depuis la BDD
- **Ajouter** un service : nom, prix (€), durée (min), photo (upload)
- **Supprimer** un service avec confirmation
- Upload de photo : validation d'extension (jpg, jpeg, png, webp), nom unique généré automatiquement

#### Sécurité de la session
- Protection de toutes les pages admin par `$_SESSION['admin_id']`
- Déconnexion propre via `logout.php` (`session_destroy()`)
- Le bouton "Accueil" ouvre dans un nouvel onglet pour préserver la session

---

## Pages & fichiers

### Frontend

| Fichier | Rôle |
|---|---|
| `index.html` | Page d'accueil (hero, stats, services, footer) |
| `Front-end/Salon.html` | Présentation du salon, horaires, localisation |
| `Front-end/services.html` | Catalogue complet des services |
| `Front-end/Formulaire.html` | Formulaire de réservation avec gestion des retours |
| `Front-end/mentions_legales.html` | Mentions légales |
| `Front-end/politique_confidentialite.html` | Politique de confidentialité |

### Backend

| Fichier | Rôle |
|---|---|
| `config.php` | Connexion PDO sécurisée à la BDD |
| `admin_login.php` | Formulaire + traitement de la connexion admin |
| `admin_dashboard.php` | Interface complète de gestion |
| `logout.php` | Destruction de session + redirection |
| `traitement_reservation.php` | Validation + insertion d'une réservation |
| `update_rdv.php` | Mise à jour du statut d'un rendez-vous |
| `save_service.php` | Ajout ou suppression d'un service en BDD |

---

## Charte graphique

| Élément | Valeur |
|---|---|
| Couleur principale | `#FFCC00` (or) |
| Fond | `#000000` (noir) |
| Texte | `#F8F8F8` (blanc cassé) |
| Police titres | Cormorant Garamond (Google Fonts) |
| Police corps | Montserrat (Google Fonts) |
| Icônes | Font Awesome 6.5.0 |

Les dégradés dorés utilisent la variable CSS `--grad-or` :
```css
--grad-or: linear-gradient(135deg, #FFCC00, #ffe14d, #b89900);
```

---

## Sécurité

- **Injections SQL** : toutes les requêtes utilisent des requêtes préparées PDO avec paramètres nommés (`:param`)
- **XSS** : toutes les données affichées sont échappées avec `htmlspecialchars()`
- **Authentification** : mots de passe hashés avec `password_hash()` (bcrypt), vérifiés avec `password_verify()`
- **Protection des pages admin** : vérification de `$_SESSION['admin_id']` à chaque chargement
- **Upload de fichiers** : validation de l'extension, nom de fichier régénéré avec `uniqid()` pour éviter les collisions et l'exécution de fichiers malveillants
- **Validation double** : chaque donnée du formulaire est validée côté client (HTML5 + JS) **et** côté serveur (PHP)

---

## À venir / Améliorations possibles

- [ ] Notifications en temps réel pour les nouveaux RDV (badge dans la sidebar)
- [ ] Filtrage et tri des réservations par date, service ou client
- [ ] Édition d'un service existant (modifier nom, prix, photo)
- [ ] Pagination du tableau des réservations
- [ ] Export CSV des réservations
- [ ] Système de créneaux horaires avec gestion des disponibilités
- [ ] Page de confirmation de réservation dédiée (au lieu d'une bannière)
- [ ] Envoi d'email de rappel 24h avant le rendez-vous
- [ ] Support multi-administrateurs avec rôles

---

## Auteurs

Projet réalisé dans le cadre d'un apprentissage du développement web full-stack (HTML/CSS/PHP/MySQL).

---

*© 2026 Johnny Barber — Tous droits réservés*

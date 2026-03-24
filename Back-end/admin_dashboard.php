<?php
session_start();
require_once __DIR__ . '/config.php';

// Protection
if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}

// ── Récupérer les réservations avec JOIN sur services ──
$rdvs = $pdo->query("
    SELECT r.id, r.nom, r.email, r.telephone, r.date, r.heure, r.message, r.statut,
           s.nom AS service
    FROM reservations r
    LEFT JOIN services s ON r.service_id = s.id
    ORDER BY r.date ASC, r.heure ASC
")->fetchAll(PDO::FETCH_ASSOC);

// ── Compteurs ──
$nb_attente  = count(array_filter($rdvs, fn($r) => $r['statut'] === 'attente'));
$nb_acceptes = count(array_filter($rdvs, fn($r) => $r['statut'] === 'accepte'));
$nb_refuses  = count(array_filter($rdvs, fn($r) => $r['statut'] === 'refuse'));

// ── Récupérer les services ──
$services = $pdo->query("SELECT * FROM services ORDER BY id ASC")->fetchAll(PDO::FETCH_ASSOC);

// ── Messages flash ──
$flash_rdv     = isset($_GET['statut'])  && $_GET['statut']  === 'ok';
$flash_service = isset($_GET['service']) && $_GET['service'] === 'ok';
$flash_erreur  = $_GET['erreur'] ?? null;
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard — Johnny Barber</title>
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,300;0,400;1,300;1,400&family=Montserrat:wght@300;400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="../styles.css">
    <style>
        .flash {
            padding: 12px 20px;
            border-radius: 6px;
            margin-bottom: 20px;
            font-family: 'Montserrat', sans-serif;
            font-size: 0.88rem;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .flash.ok     { background: #0d2b0d; border-left: 4px solid #4caf50; color: #a5d6a7; }
        .flash.erreur { background: #2b0d0d; border-left: 4px solid #c62828; color: #ef9a9a; }

        /* Filtre statut */
        .filtres {
            display: flex;
            gap: 10px;
            margin-bottom: 18px;
            flex-wrap: wrap;
        }
        .filtre-btn {
            padding: 6px 16px;
            border-radius: 20px;
            border: 1px solid #b8960c;
            background: transparent;
            color: #b8960c;
            font-family: 'Montserrat', sans-serif;
            font-size: 0.8rem;
            cursor: pointer;
            transition: all 0.2s;
        }
        .filtre-btn.active, .filtre-btn:hover {
            background: #b8960c;
            color: #000;
        }
    </style>
</head>
<body class="admin">

    <!-- ===== SIDEBAR ===== -->
    <aside class="sidebar">
        <div class="sidebar-logo">
            <h2 class="test">Johnny Barber</h2>
            <p>Administration</p>
        </div>
        <nav>
            <a href="#rdv" class="active"><i class="fa-regular fa-calendar-check"></i> Rendez-vous</a>
            <a href="#services"><i class="fa-solid fa-scissors"></i> Services</a>
        </nav>
        <div class="sidebar-footer">
            <a href="../index.html" class="btn-retour" target="_blank">
                <i class="fa-solid fa-arrow-left"></i> Accueil
            </a>
            <a href="logout.php" class="btn-logout">
                <i class="fa-solid fa-right-from-bracket"></i> Déconnexion
            </a>
        </div>
    </aside>

    <!-- ===== CONTENU ===== -->
    <main class="admin-main">

        <!-- ══════════ RENDEZ-VOUS ══════════ -->
        <div id="rdv">
            <div class="admin-header">
                <h1>Rendez-vous</h1>
                <p>Catalogue des demandes de réservation</p>
            </div>

            <?php if ($flash_rdv): ?>
                <div class="flash ok"><i class="fa-solid fa-circle-check"></i> Statut mis à jour avec succès.</div>
            <?php endif; ?>
            <?php if ($flash_erreur): ?>
                <div class="flash erreur"><i class="fa-solid fa-circle-exclamation"></i> Erreur : <?= htmlspecialchars($flash_erreur) ?></div>
            <?php endif; ?>

            <!-- Compteurs -->
            <div class="admin-stats">
                <div class="admin-stat attente">
                    <h2><?= $nb_attente ?></h2>
                    <p>En attente</p>
                </div>
                <div class="admin-stat ok">
                    <h2><?= $nb_acceptes ?></h2>
                    <p>Acceptés</p>
                </div>
                <div class="admin-stat no">
                    <h2><?= $nb_refuses ?></h2>
                    <p>Refusés</p>
                </div>
            </div>

            <!-- Filtres -->
            <div class="filtres">
                <button class="filtre-btn active" onclick="filtrer('tous', this)">Tous (<?= count($rdvs) ?>)</button>
                <button class="filtre-btn" onclick="filtrer('attente', this)">En attente (<?= $nb_attente ?>)</button>
                <button class="filtre-btn" onclick="filtrer('accepte', this)">Acceptés (<?= $nb_acceptes ?>)</button>
                <button class="filtre-btn" onclick="filtrer('refuse', this)">Refusés (<?= $nb_refuses ?>)</button>
            </div>

            <!-- Tableau -->
            <div class="table-wrapper">
                <table>
                    <thead>
                        <tr>
                            <th>Client</th>
                            <th>Contact</th>
                            <th>Service</th>
                            <th>Date & Heure</th>
                            <th>Statut</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="tbody-rdv">
                        <?php if (empty($rdvs)): ?>
                        <tr>
                            <td colspan="6" style="text-align:center; padding: 30px; opacity: 0.5;">
                                Aucune réservation pour le moment.
                            </td>
                        </tr>
                        <?php else: ?>
                        <?php foreach ($rdvs as $rdv): ?>
                        <tr data-statut="<?= $rdv['statut'] ?>">
                            <td><?= htmlspecialchars($rdv['nom']) ?></td>
                            <td>
                                <?= htmlspecialchars($rdv['email']) ?>
                                <div class="td-sub"><?= htmlspecialchars($rdv['telephone']) ?></div>
                            </td>
                            <td><?= htmlspecialchars($rdv['service'] ?? '—') ?></td>
                            <td>
                                <?= htmlspecialchars(date('d/m/Y', strtotime($rdv['date']))) ?>
                                <div class="td-sub td-or"><?= htmlspecialchars(substr($rdv['heure'], 0, 5)) ?></div>
                            </td>
                            <td>
                                <?php if ($rdv['statut'] === 'attente'): ?>
                                    <span class="badge attente">En attente</span>
                                <?php elseif ($rdv['statut'] === 'accepte'): ?>
                                    <span class="badge accepte">Accepté</span>
                                <?php else: ?>
                                    <span class="badge refuse">Refusé</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ($rdv['statut'] === 'attente'): ?>
                                <div class="actions">
                                    <form method="POST" action="update_rdv.php" style="display:inline">
                                        <input type="hidden" name="id"     value="<?= $rdv['id'] ?>">
                                        <input type="hidden" name="statut" value="accepte">
                                        <button type="submit" class="btn btn-ok"><i class="fa-solid fa-check"></i> Accepter</button>
                                    </form>
                                    <form method="POST" action="update_rdv.php" style="display:inline">
                                        <input type="hidden" name="id"     value="<?= $rdv['id'] ?>">
                                        <input type="hidden" name="statut" value="refuse">
                                        <button type="submit" class="btn btn-no"><i class="fa-solid fa-xmark"></i> Refuser</button>
                                    </form>
                                </div>
                                <?php else: ?>
                                    <div class="actions">
                                        <form method="POST" action="update_rdv.php" style="display:inline">
                                            <input type="hidden" name="id"     value="<?= $rdv['id'] ?>">
                                            <input type="hidden" name="statut" value="attente">
                                            <button type="submit" class="btn btn-retour-attente"><i class="fa-solid fa-rotate-left"></i> Remettre en attente</button>
                                        </form>
                                    </div>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- ══════════ SERVICES ══════════ -->
        <div id="services">
            <div class="admin-header">
                <h1>Gestion des services</h1>
                <p>Ajouter ou supprimer les prestations</p>
            </div>

            <?php if ($flash_service): ?>
                <div class="flash ok"><i class="fa-solid fa-circle-check"></i> Service ajouté avec succès.</div>
            <?php endif; ?>

            <!-- Formulaire ajout -->
            <div class="admin-form-card">
                <h2>Nouveau service</h2>
                <form method="POST" action="save_service.php" enctype="multipart/form-data">
                    <input type="hidden" name="action" value="ajouter">
                    <div class="admin-form-grid">
                        <div class="admin-form-group">
                            <label>Nom du service</label>
                            <input type="text" name="nom" placeholder="Ex : Coupe homme" required>
                        </div>
                        <div class="admin-form-group">
                            <label>Prix (€)</label>
                            <input type="number" name="prix" placeholder="Ex : 25" min="0" step="0.01" required>
                        </div>
                        <div class="admin-form-group">
                            <label>Durée (minutes)</label>
                            <input type="number" name="duree" placeholder="Ex : 60" min="1" required>
                        </div>
                        <div class="admin-form-group">
                            <label>Photo</label>
                            <input type="file" name="photo" accept="image/*">
                        </div>
                    </div>
                    <button type="submit" class="btn-ajouter">+ Ajouter le service</button>
                </form>
            </div>

            <!-- Grille des services depuis la BDD -->
            <h2 class="section-title">Services actuels (<?= count($services) ?>)</h2>
            <div class="admin-produits-grid">
                <?php if (empty($services)): ?>
                    <p style="opacity:0.5; font-family:'Montserrat',sans-serif;">Aucun service enregistré.</p>
                <?php else: ?>
                <?php foreach ($services as $svc): ?>
                <div class="admin-produit-card">
                    <?php if (!empty($svc['photo'])): ?>
                        <img src="../images/<?= htmlspecialchars($svc['photo']) ?>" alt="<?= htmlspecialchars($svc['nom']) ?>">
                    <?php else: ?>
                        <div style="height:160px; background:#1a1a1a; display:flex; align-items:center; justify-content:center; color:#555;">
                            <i class="fa-solid fa-image" style="font-size:2rem;"></i>
                        </div>
                    <?php endif; ?>
                    <div class="admin-produit-info">
                        <h3><?= htmlspecialchars($svc['nom']) ?></h3>
                        <div class="admin-produit-meta">
                            <span><?= number_format((float)$svc['prix'], 2, ',', '') ?> €</span>
                            <span><?= (int)$svc['duree'] ?> min</span>
                        </div>
                        <form method="POST" action="save_service.php" onsubmit="return confirm('Supprimer ce service ?')">
                            <input type="hidden" name="action" value="supprimer">
                            <input type="hidden" name="id"     value="<?= $svc['id'] ?>">
                            <button type="submit" class="btn-suppr">Supprimer</button>
                        </form>
                    </div>
                </div>
                <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>

    </main>

    <script>
        // ── Filtre des lignes du tableau par statut ──
        function filtrer(statut, btn) {
            document.querySelectorAll('.filtre-btn').forEach(b => b.classList.remove('active'));
            btn.classList.add('active');

            document.querySelectorAll('#tbody-rdv tr[data-statut]').forEach(tr => {
                tr.style.display = (statut === 'tous' || tr.dataset.statut === statut) ? '' : 'none';
            });
        }

        // ── Nettoyage de l'URL après flash ──
        if (window.location.search) {
            window.history.replaceState({}, '', window.location.pathname + window.location.hash);
        }
    </script>

</body>
</html>
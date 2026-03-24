<?php
session_start();
require_once __DIR__ . '/config.php';

if (!isset($_SESSION['admin_id'])) {
    http_response_code(403);
    exit('Non autorisé');
}

$action = $_POST['action'] ?? '';

// ── Suppression ──
if ($action === 'supprimer') {
    $id = (int) ($_POST['id'] ?? 0);
    if ($id) {
        $stmt = $pdo->prepare("DELETE FROM services WHERE id = :id");
        $stmt->execute([':id' => $id]);
    }
    header("Location: admin_dashboard.php#services");
    exit();
}

// ── Ajout ──
if ($action === 'ajouter') {
    $nom   = trim($_POST['nom']   ?? '');
    $prix  = str_replace(',', '.', trim($_POST['prix']  ?? ''));
    $duree = (int) ($_POST['duree'] ?? 0);

    if (empty($nom) || !is_numeric($prix) || $duree <= 0) {
        header("Location: admin_dashboard.php?erreur=champs_vides#services");
        exit();
    }

    // ── Gestion de l'upload photo ──
    $nom_fichier = null;

    if (!empty($_FILES['photo']['name'])) {
        $extensions_autorisees = ['jpg', 'jpeg', 'png', 'webp'];
        $ext = strtolower(pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION));

        if (!in_array($ext, $extensions_autorisees)) {
            header("Location: admin_dashboard.php?erreur=format_image_invalide#services");
            exit();
        }

        // Nom unique pour éviter les collisions
        $nom_fichier = uniqid('service_') . '.' . $ext;
        $destination = __DIR__ . '/../images/' . $nom_fichier;

        if (!move_uploaded_file($_FILES['photo']['tmp_name'], $destination)) {
            header("Location: admin_dashboard.php?erreur=upload_echoue#services");
            exit();
        }
    }

    $stmt = $pdo->prepare("INSERT INTO services (nom, prix, duree, photo) VALUES (:nom, :prix, :duree, :photo)");
    $stmt->execute([
        ':nom'   => htmlspecialchars($nom),
        ':prix'  => (float) $prix,
        ':duree' => $duree,
        ':photo' => $nom_fichier,
    ]);

    header("Location: admin_dashboard.php?service=ok#services");
    exit();
}

header("Location: admin_dashboard.php");
exit();
?>
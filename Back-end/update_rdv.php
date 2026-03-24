<?php
session_start();
require_once __DIR__ . '/config.php';

// Protection
if (!isset($_SESSION['admin_id'])) {
    http_response_code(403);
    exit('Non autorisé');
}

$id     = isset($_POST['id'])     ? (int) $_POST['id']     : 0;
$statut = isset($_POST['statut']) ? trim($_POST['statut'])  : '';

$statuts_valides = ['accepte', 'refuse', 'attente'];

if (!$id || !in_array($statut, $statuts_valides)) {
    header("Location: admin_dashboard.php?erreur=parametres_invalides");
    exit();
}

$stmt = $pdo->prepare("UPDATE reservations SET statut = :statut WHERE id = :id");
$stmt->execute([':statut' => $statut, ':id' => $id]);

header("Location: admin_dashboard.php?statut=ok");
exit();
?>
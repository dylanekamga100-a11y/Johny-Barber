<?php

// ============================================================
//  traitement_reservation.php — Johnny Barber
//  Reçoit les données du formulaire, valide, insère en BDD,
//  puis redirige avec un statut.
// ============================================================

// ── ÉTAPE 1 : Vérifier que le formulaire a bien été envoyé ──
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: ../Front-end/Formulaire.html");
    exit();
}

// ── ÉTAPE 2 : Récupérer et nettoyer les données ──
$nom       = trim($_POST["nom"]       ?? "");
$email     = trim($_POST["email"]     ?? "");
$telephone = trim($_POST["telephone"] ?? "");
$service   = trim($_POST["service"]   ?? "");
$date      = trim($_POST["date"]      ?? "");
$heure     = trim($_POST["heure"]     ?? "");
$message   = trim($_POST["message"]   ?? "");

// ── ÉTAPE 3 : Validation côté serveur ──
$erreurs = [];

if (empty($nom))                                  $erreurs[] = "Le nom est obligatoire.";
if (empty($email))                                $erreurs[] = "L'email est obligatoire.";
if (!filter_var($email, FILTER_VALIDATE_EMAIL))   $erreurs[] = "L'adresse email n'est pas valide.";
if (empty($telephone))                            $erreurs[] = "Le téléphone est obligatoire.";
if (empty($service) || $service === "Choisir un service") $erreurs[] = "Veuillez choisir un service.";
if (empty($date))                                 $erreurs[] = "La date est obligatoire.";
if (empty($heure))                                $erreurs[] = "L'heure est obligatoire.";

// Validation de la date : pas dans le passé
if (!empty($date) && strtotime($date) < strtotime(date("Y-m-d"))) {
    $erreurs[] = "La date ne peut pas être dans le passé.";
}

if (!empty($erreurs)) {
    $params = http_build_query(["statut" => "erreur", "msg" => implode("|", $erreurs)]);
    header("Location: ../Front-end/Formulaire.html?" . $params);
    exit();
}

// ── ÉTAPE 4 : Connexion à la BDD via config.php ──
require_once __DIR__ . "/config.php";
// $pdo est maintenant disponible

// ── ÉTAPE 5 : Retrouver l'id du service choisi ──
$stmt = $pdo->prepare("SELECT id FROM services WHERE nom = :nom");
$stmt->execute([":nom" => $service]);
$service_trouve = $stmt->fetch(PDO::FETCH_ASSOC);
$service_id = $service_trouve ? $service_trouve["id"] : null;

// ── ÉTAPE 6 : Insérer la réservation ──
// La table reservations stocke directement nom/email/telephone
$insert = $pdo->prepare("
    INSERT INTO reservations (nom, email, telephone, service_id, date, heure, message, statut)
    VALUES (:nom, :email, :telephone, :service_id, :date, :heure, :message, 'attente')
");

$insert->execute([
    ":nom"        => htmlspecialchars($nom),
    ":email"      => htmlspecialchars($email),
    ":telephone"  => htmlspecialchars($telephone),
    ":service_id" => $service_id,
    ":date"       => $date,
    ":heure"      => $heure,
    ":message"    => $message !== "" ? htmlspecialchars($message) : null,
]);

// ── ÉTAPE 7 : Email au salon (optionnel, ne bloque pas la redirection) ──
$email_salon = "contact@johnnybarber.com";
$sujet_salon = "Nouvelle réservation — " . $nom . " (" . $service . ")";
$corps_salon = "Nouvelle réservation reçue.\n\n"
    . "Nom      : $nom\n"
    . "Email    : $email\n"
    . "Téléphone: $telephone\n"
    . "Service  : $service\n"
    . "Date     : $date\n"
    . "Heure    : $heure\n"
    . "Message  : " . ($message ?: "Aucun") . "\n";
$entetes_salon = "From: noreply@johnnybarber.com\r\nReply-To: $email\r\nContent-Type: text/plain; charset=UTF-8\r\n";
@mail($email_salon, $sujet_salon, $corps_salon, $entetes_salon);

// ── ÉTAPE 8 : Email de confirmation au client ──
$sujet_client = "Confirmation de votre réservation — Johnny Barber";
$corps_client = "Bonjour $nom,\n\n"
    . "Nous avons bien reçu votre demande. Récapitulatif :\n\n"
    . "Service  : $service\n"
    . "Date     : $date\n"
    . "Heure    : $heure\n\n"
    . "Nous vous confirmerons votre rendez-vous dans les plus brefs délais.\n\n"
    . "L'équipe Johnny Barber\n"
    . "📞 06 12 34 56 78 | 📍 12 rue de la Paix, 75002 Paris";
$entetes_client = "From: contact@johnnybarber.com\r\nContent-Type: text/plain; charset=UTF-8\r\n";
@mail($email, $sujet_client, $corps_client, $entetes_client);

// ── ÉTAPE 9 : Redirection succès ──
header("Location: ../Front-end/Formulaire.html?statut=succes&prenom=" . urlencode(explode(" ", $nom)[0]));
exit();

?>
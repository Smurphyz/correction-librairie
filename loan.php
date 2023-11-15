<?php
require('config.php');

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}


if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Requête SQL pour récupérer les emprunts de l'utilisateur actuellement connecté
$user_id = $_SESSION['user_id'];
$query = "SELECT emprunt.*, livres.titre FROM emprunt 
    INNER JOIN livres ON emprunt.id_livre = livres.id 
    WHERE emprunt.id_utilisateur = :user_id";
$stmt = $pdo->prepare($query);
$stmt->execute(array(':user_id' => $user_id));
$emprunts = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Requête SQL pour récupérer les emprunts de l'utilisateur
$user_id = $_SESSION['user_id'];
$query = "SELECT emprunt.*, livres.titre, DATEDIFF(CURRENT_DATE, emprunt.date_emprunt) AS duree_detention FROM emprunt 
    INNER JOIN livres ON emprunt.id_livre = livres.id 
    WHERE emprunt.id_utilisateur = :user_id";
$stmt = $pdo->prepare($query);
$stmt->execute(array(':user_id' => $user_id));
$emprunts = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Liste des Emprunts</title>
    <link rel="stylesheet" type="text/css" href="css/style.css">
</head>
<body>
<header>
        <h1>Liste des Emprunts - Librairie XYZ</h1>
    </header>
    <table>
        <tr>
            <th>Livre</th>
            <th>Date d'emprunt</th>
            <th>Date de retour prévue</th>
            <th>Date de retour effectif</th>
        </tr>
        <?php foreach ($emprunts as $emprunt) : ?>
            <tr>
                <td><?= $emprunt['titre'] ?></td>
                <td><?= $emprunt['date_emprunt'] ?></td>
                <td><?= $emprunt['date_retour_prevue'] ?></td>
                <td><?= $emprunt['date_retour_effectif'] ?></td>
            </tr>
        <?php endforeach; ?>
    </table>

      <!-- Affichez l'alerte ici -->
      <div id="alerte" style="display: none; background-color: yellow; text-align: center;">
        Attention : Vous détenez un livre depuis plus de 30 jours. Merci de le retourner dès que possible.
    </div>

    <button onclick="window.location.href ='index.php'">Retour à l'accueil</button>
    <button onclick="window.location.href ='new_loan.php'">Effectuer un nouvel emprunt</button>
    <?php if (!empty($emprunts)) : ?>
        <button onclick="window.location.href ='return_loan.php'">Effectuer un retour</button>
    <?php endif; ?>
</body>
<script>
    // Boucle à travers les emprunts et affiche l'alerte si nécessaire
    <?php foreach ($emprunts as $emprunt) : ?>
        <?php if ($emprunt['duree_detention'] > 30) : ?>
            document.getElementById('alerte').style.display = 'block';
        <?php endif; ?>
    <?php endforeach; ?>
</script>
</html>

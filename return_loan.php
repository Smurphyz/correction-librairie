<?php
require('config.php');
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user_id = $_SESSION['user_id'];
    $emprunt_id = $_POST['book_id']; // Utilisez l'ID de l'emprunt

    // Effectuez les validations nécessaires

    // Requête SQL pour marquer l'emprunt comme retourné
    $query = "UPDATE emprunt SET date_retour_effectif = CURRENT_DATE WHERE id_utilisateur = :user_id AND id_emprunt = :emprunt_id";
    $stmt = $pdo->prepare($query);
    $stmt->execute(array(':user_id' => $user_id, ':emprunt_id' => $emprunt_id));

    // Après avoir marqué l'emprunt comme retourné, vous devez récupérer l'ID du livre associé à cet emprunt
    $query = "SELECT id_livre FROM emprunt WHERE id_emprunt = :emprunt_id";
    $stmt = $pdo->prepare($query);
    $stmt->execute(array(':emprunt_id' => $emprunt_id));
    $row = $stmt->fetch();

    if ($row) {
        $book_id = $row['id_livre'];
        // Ensuite, vous pouvez mettre à jour le statut du livre
        $updateQuery = "UPDATE livres SET statut = 'disponible' WHERE id = :book_id";
        $updateStmt = $pdo->prepare($updateQuery);
        $updateStmt->execute(array(':book_id' => $book_id));
    }
}

// Requête SQL pour récupérer les emprunts en cours de l'utilisateur
$user_id = $_SESSION['user_id'];
$query = "SELECT emprunt.id_emprunt, livres.titre FROM emprunt
          INNER JOIN livres ON emprunt.id_livre = livres.id
          WHERE emprunt.id_utilisateur = :user_id AND emprunt.date_retour_effectif IS NULL";
$stmt = $pdo->prepare($query);
$stmt->execute(array(':user_id' => $user_id));
$emprunts_en_cours = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>
<!DOCTYPE html>
<html>
<head>
    <title>Retour d'Emprunt</title>
    <link rel="stylesheet" type="text/css" href="css/style.css">
</head>
<body>
    <header>
        <h1>Retour d'emprunt - Librairie XYZ</h1>
    </header>
    <form method="post">
        <label for="book_id">Emprunt à retourner :</label>
        <select name="book_id" required>
            <?php foreach ($emprunts_en_cours as $emprunt) : ?>
                <option value="<?= $emprunt['id_emprunt'] ?>"><?= $emprunt['titre'] ?></option>
            <?php endforeach; ?>
        </select>
        <br>
        <button type="submit">Retourner</button>
    </form>
    
    <button onclick="window.location.href ='loan.php'">Voir mes emprunts</button>
    <button onclick="window.location.href ='index.php'">Retour à l'accueil</button>
</body>
</html>

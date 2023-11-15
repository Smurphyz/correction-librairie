<?php
require('config.php');
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}


if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Traitement du formulaire pour créer un nouvel emprunt
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user_id = $_SESSION['user_id'];
    $book_id = $_POST['book_id'];
    $date_emprunt = date('Y-m-d');
    $date_retour_prevue = $_POST['date_retour'];

    // Effectuez les validations nécessaires
    // Vérifiez si le livre est disponible avant d'ajouter un nouvel emprunt
$bookQuery = "SELECT statut FROM livres WHERE id = :book_id";
$bookStmt = $pdo->prepare($bookQuery);
$bookStmt->execute(array(':book_id' => $book_id));
$book = $bookStmt->fetch();

if ($book['statut'] === 'disponible') {
    // Autorisez l'emprunt

    // Requête SQL pour insérer le nouvel emprunt dans la base de données
    $query = "INSERT INTO emprunt (id_utilisateur, id_livre, date_emprunt, date_retour_prevue) 
              VALUES (:user_id, :book_id, :date_emprunt, :date_retour_prevue)";
    $stmt = $pdo->prepare($query);
    $stmt->execute(array(
        ':user_id' => $user_id,
        ':book_id' => $book_id,
        ':date_emprunt' => $date_emprunt,
        ':date_retour_prevue' => $date_retour_prevue
    ));
    header('Location: loan.php');
} else {
    // Affichez un message d'erreur
    echo"Le livre n'est pas disponible";
}



    // Après avoir inséré l'emprunt, mettez à jour le statut du livre
$updateQuery = "UPDATE livres SET statut = 'emprunté' WHERE id = :book_id";
$updateStmt = $pdo->prepare($updateQuery);
$updateStmt->execute(array(':book_id' => $book_id));

}

// Requête SQL pour récupérer la liste des livres disponibles à l'emprunt
$query = "SELECT id, titre FROM livres WHERE statut = 'disponible'";
$stmt = $pdo->query($query);
$livres_disponibles = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Nouvel Emprunt</title>
    <link rel="stylesheet" type="text/css" href="css/style.css">
</head>
<body>
<header>
        <h1>Nouvel Emprunt  - Librairie XYZ</h1>
    </header>
    <form method="post">
        <label for="book_id">Livre :</label>
        <select name="book_id" required>
            <?php foreach ($livres_disponibles as $livre) : ?>
                <option value="<?= $livre['id'] ?>"><?= $livre['titre'] ?></option>
            <?php endforeach; ?>
        </select>
        <br>
        <label for="date_retour">Date de retour prévue :</label>
        <input type="date" name="date_retour" required>
        <br>
        <button type="submit">Emprunter</button>
    </form>
    <button onclick="window.location.href ='loan.php'">Voir mes emprunts</a>
    <button onclick="window.location.href ='index.php'">Retour à l'accueil</a>
</body>
</html>

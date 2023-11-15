<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}


$host = 'sql104.infinityfree.com';
$dbname = 'if0_35208375_library';
$username = 'if0_35208375';
$password = 'XAX0TS2EGJ';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erreur de connexion à la base de données : " . $e->getMessage());
}
?>

<?php
// Configuration de la base de données
$host = 'localhost';
$dbname = 'gestion_emploi_temps';
$username = 'root';
$password = '';

try {
    // Connexion à la base de données avec PDO
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    
    // Configuration des options PDO
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    
    // Création du dossier pour les fichiers XML s'il n'existe pas
    if (!file_exists('emplois_xml')) {
        mkdir('emplois_xml', 0777, true);
    }
    
} catch(PDOException $e) {
    die("Erreur de connexion : " . $e->getMessage());
}
?> 
<?php
// Configuration de la base de données
$host = 'localhost';
$dbname = 'gestion_emploi_temps';
$username = 'root';
$password = '';

try {
    // Connexion à la base de données
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Requête pour obtenir la liste des classes avec leurs filières
    $stmt = $pdo->query("
        SELECT 
            c.ID_CLASSE as id,
            CONCAT(f.NOM_FILIERE, ' ', c.NIVEAU, 'ème année') as nom
        FROM classes c
        JOIN filieres f ON c.ID_FILIERE = f.ID_FILIERE
        ORDER BY f.NOM_FILIERE, c.NIVEAU
    ");
    
    // Récupération des résultats
    $classes = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Envoi de l'en-tête JSON
    header('Content-Type: application/json');
    
    // Conversion et envoi des données en JSON
    echo json_encode($classes);
    
} catch(PDOException $e) {
    // En cas d'erreur, on renvoie un JSON avec l'erreur
    header('Content-Type: application/json');
    http_response_code(500);
    echo json_encode(['error' => 'Erreur : ' . $e->getMessage()]);
} 
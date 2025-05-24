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
    
    // Requête pour calculer le total des heures par professeur
    $stmt = $pdo->query("
        SELECT 
            p.NOM_PROF,
            SUM(
                TIME_TO_SEC(TIMEDIFF(c.HEURE_FIN, c.HEURE_DEBUT)) / 3600
            ) as total_heures
        FROM cours c
        JOIN professeurs p ON c.ID_PROF = p.ID_PROF
        GROUP BY p.ID_PROF, p.NOM_PROF
        ORDER BY total_heures DESC
    ");
    
    // Récupération des résultats
    $charges = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Préparation des données pour Chart.js
    $data = [
        'labels' => [],
        'heures' => []
    ];
    
    foreach ($charges as $charge) {
        $data['labels'][] = $charge['NOM_PROF'];
        $data['heures'][] = round($charge['total_heures'], 2);
    }
    
    // Envoi de l'en-tête JSON
    header('Content-Type: application/json');
    
    // Conversion et envoi des données en JSON
    echo json_encode($data);
    
} catch(PDOException $e) {
    // En cas d'erreur, on renvoie un JSON avec l'erreur
    header('Content-Type: application/json');
    http_response_code(500);
    echo json_encode(['error' => 'Erreur : ' . $e->getMessage()]);
} 
<?php
require_once 'config.php';

header('Content-Type: application/json');

try {
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
    $charges = $stmt->fetchAll();
    
    // Préparation des données pour Chart.js
    $data = [
        'labels' => [],
        'heures' => []
    ];
    
    foreach ($charges as $charge) {
        $data['labels'][] = $charge['NOM_PROF'];
        $data['heures'][] = round($charge['total_heures'], 2);
    }
    
    // Envoi des données en JSON
    echo json_encode($data);
    
} catch(PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'error' => true,
        'message' => 'Erreur lors de la récupération des charges horaires : ' . $e->getMessage()
    ]);
}
?> 
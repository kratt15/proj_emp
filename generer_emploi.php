<?php
require_once 'config.php';

header('Content-Type: application/json');

if (!isset($_GET['classe'])) {
    echo json_encode(['success' => false, 'message' => 'ID de classe non spécifié']);
    exit;
}

$id_classe = intval($_GET['classe']);

try {
    // Récupération des informations de la classe
    $stmt_classe = $pdo->prepare("
        SELECT c.ID_CLASSE, f.NOM_FILIERE, c.NIVEAU 
        FROM classes c 
        JOIN filieres f ON c.ID_FILIERE = f.ID_FILIERE 
        WHERE c.ID_CLASSE = ?
    ");
    $stmt_classe->execute([$id_classe]);
    $classe_info = $stmt_classe->fetch();
    
    if (!$classe_info) {
        echo json_encode(['success' => false, 'message' => 'Classe non trouvée']);
        exit;
    }
    
    // Création du document XML
    $xml = new DOMDocument('1.0', 'UTF-8');
    $xml->formatOutput = true;
    
    // Création de l'élément racine
    $emploi = $xml->createElement('emploi');
    $classe_nom = $classe_info['NOM_FILIERE'] . $classe_info['NIVEAU'];
    $emploi->setAttribute('classe', $classe_nom);
    $xml->appendChild($emploi);
    
    // Requête pour obtenir l'emploi du temps
    $stmt = $pdo->prepare("
        SELECT 
            c.JOUR,
            c.HEURE_DEBUT as debut,
            c.HEURE_FIN as fin,
            p.NOM_PROF as prof,
            m.ID_MODULE as module,
            s.NOM_SALLE as salle
        FROM cours c
        JOIN professeurs p ON c.ID_PROF = p.ID_PROF
        JOIN modules m ON c.ID_MODULE = m.ID_MODULE
        JOIN salles s ON c.ID_SALLE = s.ID_SALLE
        WHERE c.ID_CLASSE = ?
        ORDER BY 
            CASE 
                WHEN JOUR = 'Lundi' THEN 1
                WHEN JOUR = 'Mardi' THEN 2
                WHEN JOUR = 'Mercredi' THEN 3
                WHEN JOUR = 'Jeudi' THEN 4
                WHEN JOUR = 'Vendredi' THEN 5
                WHEN JOUR = 'Samedi' THEN 6
            END,
            c.HEURE_DEBUT
    ");
    $stmt->execute([$id_classe]);
    
    while ($row = $stmt->fetch()) {
        $seance = $xml->createElement('seance');
        $seance->setAttribute('jour', strtolower($row['JOUR']));
        $seance->setAttribute('debut', substr($row['debut'], 0, 5));
        $seance->setAttribute('fin', substr($row['fin'], 0, 5));
        $seance->setAttribute('prof', $row['prof']);
        $seance->setAttribute('module', $row['module']);
        $seance->setAttribute('salle', $row['salle']);
        $emploi->appendChild($seance);
    }
    
    // Création du dossier si nécessaire
    if (!file_exists('emplois_xml')) {
        mkdir('emplois_xml', 0777, true);
    }
    
    // Sauvegarde du fichier XML
    $filename = 'emplois_xml/emploi_' . $id_classe . '.xml';
    if ($xml->save($filename)) {
        echo json_encode([
            'success' => true, 
            'message' => 'Fichier XML généré avec succès',
            'filename' => $filename
        ]);
    } else {
        echo json_encode([
            'success' => false, 
            'message' => 'Erreur lors de la sauvegarde du fichier XML'
        ]);
    }
    
} catch(PDOException $e) {
    echo json_encode([
        'success' => false, 
        'message' => 'Erreur de base de données : ' . $e->getMessage()
    ]);
} catch(Exception $e) {
    echo json_encode([
        'success' => false, 
        'message' => 'Erreur : ' . $e->getMessage()
    ]);
}
?> 
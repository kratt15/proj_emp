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
    
    // Récupération de l'ID de la classe depuis l'URL
    $id_classe = isset($_GET['classe']) ? intval($_GET['classe']) : 1;
    
    // Requête pour obtenir les informations de la classe
    $stmt_classe = $pdo->prepare("
        SELECT c.ID_CLASSE, f.NOM_FILIERE, c.NIVEAU 
        FROM classes c 
        JOIN filieres f ON c.ID_FILIERE = f.ID_FILIERE 
        WHERE c.ID_CLASSE = ?
    ");
    $stmt_classe->execute([$id_classe]);
    $classe_info = $stmt_classe->fetch(PDO::FETCH_ASSOC);
    
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
    
    // Création du document XML
    $xml = new DOMDocument('1.0', 'UTF-8');
    $xml->formatOutput = true;
    
    // Création de l'élément racine
    $emploi = $xml->createElement('emploi');
    $classe_nom = $classe_info['NOM_FILIERE'] . $classe_info['NIVEAU'];
    $emploi->setAttribute('classe', $classe_nom);
    $xml->appendChild($emploi);
    
    // Ajout des séances
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $seance = $xml->createElement('seance');
        $seance->setAttribute('jour', strtolower($row['JOUR']));
        $seance->setAttribute('debut', substr($row['debut'], 0, 5));
        $seance->setAttribute('fin', substr($row['fin'], 0, 5));
        $seance->setAttribute('prof', $row['prof']);
        $seance->setAttribute('module', $row['module']);
        $seance->setAttribute('salle', $row['salle']);
        $emploi->appendChild($seance);
    }
    
    // Définition de l'en-tête HTTP pour XML
    header('Content-Type: application/xml; charset=utf-8');
    
    // Affichage du XML
    echo $xml->saveXML();
    
} catch(PDOException $e) {
    // En cas d'erreur, on renvoie un XML d'erreur
    header('Content-Type: application/xml; charset=utf-8');
    echo '<?xml version="1.0" encoding="UTF-8"?>';
    echo '<erreur>Une erreur est survenue : ' . htmlspecialchars($e->getMessage()) . '</erreur>';
}
?> 
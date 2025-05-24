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
        SELECT 
            c.NIVEAU,
            f.NOM_FILIERE as filiere
        FROM classes c
        JOIN filieres f ON c.ID_FILIERE = f.ID_FILIERE
        WHERE c.ID_CLASSE = ?
    ");
    $stmt_classe->execute([$id_classe]);
    $classe_info = $stmt_classe->fetch(PDO::FETCH_ASSOC);
    
    // Requête pour obtenir les étudiants de la classe
    $stmt_etudiants = $pdo->prepare("
        SELECT 
            NUM_INSCRIPTION as numInscription,
            NOM_ET as nom,
            PRENOM_ET as prenom
        FROM etudiants
        WHERE ID_CLASSE = ?
        ORDER BY NOM_ET, PRENOM_ET
    ");
    $stmt_etudiants->execute([$id_classe]);
    $etudiants = $stmt_etudiants->fetchAll(PDO::FETCH_ASSOC);
    
    // Requête pour obtenir les modules enseignés dans cette classe
    $stmt_modules = $pdo->prepare("
        SELECT DISTINCT
            m.ID_MODULE as idModule,
            m.NOM_MODULE as nomModule
        FROM cours c
        JOIN modules m ON c.ID_MODULE = m.ID_MODULE
        WHERE c.ID_CLASSE = ?
        ORDER BY m.ID_MODULE
    ");
    $stmt_modules->execute([$id_classe]);
    $modules = $stmt_modules->fetchAll(PDO::FETCH_ASSOC);
    
    // Création du document XML
    $xml = new DOMDocument('1.0', 'UTF-8');
    $xml->formatOutput = true;
    
    // Création de l'élément racine (classe)
    $classe = $xml->createElement('classe');
    $classe->setAttribute('filiere', $classe_info['filiere']);
    $classe->setAttribute('niveau', $classe_info['NIVEAU']);
    $xml->appendChild($classe);
    
    // Ajout des étudiants
    $etudiants_element = $xml->createElement('etudiants');
    $classe->appendChild($etudiants_element);
    
    foreach ($etudiants as $etudiant) {
        $etudiant_element = $xml->createElement('etudiant');
        $etudiant_element->setAttribute('numInscription', $etudiant['numInscription']);
        $etudiant_element->setAttribute('nom', $etudiant['nom']);
        $etudiant_element->setAttribute('prenom', $etudiant['prenom']);
        $etudiants_element->appendChild($etudiant_element);
    }
    
    // Ajout des modules
    $modules_element = $xml->createElement('modules');
    $classe->appendChild($modules_element);
    
    foreach ($modules as $module) {
        $module_element = $xml->createElement('module');
        $module_element->setAttribute('idModule', $module['idModule']);
        $module_element->setAttribute('nomModule', $module['nomModule']);
        $modules_element->appendChild($module_element);
    }
    
    // Envoi de l'en-tête XML
    header('Content-Type: application/xml; charset=utf-8');
    
    // Affichage du XML
    echo $xml->saveXML();
    
} catch(PDOException $e) {
    // En cas d'erreur, on renvoie un XML d'erreur
    header('Content-Type: application/xml; charset=utf-8');
    echo '<?xml version="1.0" encoding="UTF-8"?>';
    echo '<erreur>Une erreur est survenue : ' . htmlspecialchars($e->getMessage()) . '</erreur>';
} 
<?php
require_once 'config.php';

// Vérification du paramètre classe
if (!isset($_GET['classe'])) {
    die('Veuillez spécifier une classe.');
}

$id_classe = intval($_GET['classe']);

try {
    // Génération du fichier XML
    $xml_filename = 'emplois_xml/emploi_' . $id_classe . '.xml';
    
    // Si le fichier n'existe pas, on le génère
    if (!file_exists($xml_filename)) {
        require_once 'generer_emploi.php';
    }
    
    // Vérification que le fichier existe et est lisible
    if (!file_exists($xml_filename) || !is_readable($xml_filename)) {
        throw new Exception('Impossible de lire le fichier XML.');
    }
    
    // Chargement des fichiers XML et XSLT
    $xml = new DOMDocument();
    $xsl = new DOMDocument();
    
    // Chargement du XML
    $xml_content = file_get_contents($xml_filename);
    if (empty($xml_content)) {
        throw new Exception('Le fichier XML est vide.');
    }
    
    $loaded = $xml->loadXML($xml_content);
    if (!$loaded) {
        throw new Exception('Erreur lors du chargement du XML.');
    }
    
    // Chargement du XSLT
    $xsl->load('emploi_temps.xsl');
    
    // Configuration du processeur XSLT
    $proc = new XSLTProcessor();
    $proc->importStylesheet($xsl);
    
    // Application de la transformation
    $html = $proc->transformToXML($xml);
    
    if ($html === false) {
        throw new Exception('Erreur lors de la transformation XSLT.');
    }
    
    // Envoi du résultat
    echo $html;
    
} catch (Exception $e) {
    // En cas d'erreur, afficher un message d'erreur formaté
    echo '<div class="alert alert-danger">';
    echo 'Erreur : ' . htmlspecialchars($e->getMessage());
    echo '<br>Détails techniques pour le débogage :<br>';
    echo 'Fichier XML : ' . htmlspecialchars($xml_filename) . '<br>';
    if (isset($xml_content)) {
        echo 'Contenu XML :<br>';
        echo '<pre>' . htmlspecialchars($xml_content) . '</pre>';
    }
    echo '</div>';
}
?> 
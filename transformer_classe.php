<?php
try {
    // Création des objets XML et XSLT
    $xml = new DOMDocument();
    $xsl = new DOMDocument();
    
    // Récupération de l'ID de la classe
    $id_classe = isset($_GET['classe']) ? intval($_GET['classe']) : null;
    
    if (!$id_classe) {
        throw new Exception('ID de classe non spécifié');
    }
    
    // Chargement du XML depuis l'URL du générateur
    $xml_url = "http://" . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']) . "/generer_classe_xml.php?classe=" . $id_classe;
    $xml_content = file_get_contents($xml_url);
    $xml->loadXML($xml_content);
    
    // Chargement de la feuille XSLT
    $xsl->load('classe_details.xsl');
    
    // Configuration du processeur XSLT
    $proc = new XSLTProcessor();
    $proc->importStylesheet($xsl);
    
    // Application de la transformation
    $html = $proc->transformToXML($xml);
    
    // Envoi du résultat
    header('Content-Type: text/html; charset=utf-8');
    echo $html;
    
} catch (Exception $e) {
    header('Content-Type: text/html; charset=utf-8');
    echo '<div class="alert alert-danger">Une erreur est survenue : ' . htmlspecialchars($e->getMessage()) . '</div>';
} 
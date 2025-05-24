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
    
    // Récupération des données du formulaire
    $classe = isset($_POST['classe']) ? intval($_POST['classe']) : null;
    $professeur = isset($_POST['professeur']) ? intval($_POST['professeur']) : null;
    $module = isset($_POST['module']) ? $_POST['module'] : null;
    $salle = isset($_POST['salle']) ? intval($_POST['salle']) : null;
    $jour = isset($_POST['jour']) ? $_POST['jour'] : null;
    $heure_debut = isset($_POST['heure_debut']) ? $_POST['heure_debut'] : null;
    $heure_fin = isset($_POST['heure_fin']) ? $_POST['heure_fin'] : null;
    
    // Validation des données
    if (!$classe || !$professeur || !$module || !$salle || !$jour || !$heure_debut || !$heure_fin) {
        throw new Exception('Tous les champs sont obligatoires');
    }
    
    // Vérification des conflits d'horaire pour la salle
    $stmt = $pdo->prepare("
        SELECT COUNT(*) as nb
        FROM cours
        WHERE ID_SALLE = ? 
        AND JOUR = ?
        AND (
            (HEURE_DEBUT <= ? AND HEURE_FIN > ?) OR
            (HEURE_DEBUT < ? AND HEURE_FIN >= ?) OR
            (HEURE_DEBUT >= ? AND HEURE_FIN <= ?)
        )
    ");
    $stmt->execute([$salle, $jour, $heure_debut, $heure_debut, $heure_fin, $heure_fin, $heure_debut, $heure_fin]);
    $result = $stmt->fetch();
    
    if ($result['nb'] > 0) {
        throw new Exception('La salle est déjà occupée sur ce créneau');
    }
    
    // Vérification des conflits d'horaire pour le professeur
    $stmt->execute([$professeur, $jour, $heure_debut, $heure_debut, $heure_fin, $heure_fin, $heure_debut, $heure_fin]);
    if ($result['nb'] > 0) {
        throw new Exception('Le professeur est déjà occupé sur ce créneau');
    }
    
    // Vérification des conflits d'horaire pour la classe
    $stmt->execute([$classe, $jour, $heure_debut, $heure_debut, $heure_fin, $heure_fin, $heure_debut, $heure_fin]);
    if ($result['nb'] > 0) {
        throw new Exception('La classe est déjà occupée sur ce créneau');
    }
    
    // Insertion de la nouvelle séance
    $stmt = $pdo->prepare("
        INSERT INTO cours (ID_CLASSE, ID_PROF, ID_SALLE, ID_MODULE, JOUR, HEURE_DEBUT, HEURE_FIN)
        VALUES (?, ?, ?, ?, ?, ?, ?)
    ");
    $stmt->execute([$classe, $professeur, $salle, $module, $jour, $heure_debut, $heure_fin]);
    
    // Réponse en cas de succès
    header('Content-Type: application/json');
    echo json_encode([
        'success' => true,
        'message' => 'La séance a été ajoutée avec succès'
    ]);
    
} catch(Exception $e) {
    // Réponse en cas d'erreur
    header('Content-Type: application/json');
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
} 
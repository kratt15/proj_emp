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
    
    // Récupération du type de liste demandé
    $type = isset($_GET['type']) ? $_GET['type'] : '';
    
    $data = [];
    
    switch($type) {
        case 'classes':
            $stmt = $pdo->query("
                SELECT 
                    c.ID_CLASSE as id,
                    CONCAT(f.NOM_FILIERE, ' - Niveau ', c.NIVEAU) as nom
                FROM classes c
                JOIN filieres f ON c.ID_FILIERE = f.ID_FILIERE
                ORDER BY f.NOM_FILIERE, c.NIVEAU
            ");
            break;
            
        case 'professeurs':
            $stmt = $pdo->query("
                SELECT 
                    ID_PROF as id,
                    NOM_PROF as nom
                FROM professeurs
                ORDER BY NOM_PROF
            ");
            break;
            
        case 'modules':
            $stmt = $pdo->query("
                SELECT 
                    ID_MODULE as id,
                    CONCAT(ID_MODULE, ' - ', NOM_MODULE) as nom
                FROM modules
                ORDER BY ID_MODULE
            ");
            break;
            
        case 'salles':
            $stmt = $pdo->query("
                SELECT 
                    ID_SALLE as id,
                    NOM_SALLE as nom
                FROM salles
                ORDER BY NOM_SALLE
            ");
            break;
            
        default:
            throw new Exception('Type de liste non valide');
    }
    
    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Envoi de l'en-tête JSON
    header('Content-Type: application/json');
    echo json_encode($data);
    
} catch(PDOException $e) {
    header('Content-Type: application/json');
    http_response_code(500);
    echo json_encode(['error' => 'Erreur : ' . $e->getMessage()]);
} 
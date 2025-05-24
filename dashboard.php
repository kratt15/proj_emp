<?php
session_start();
require_once 'config.php';

// Fonction pour générer le fichier XML
function genererFichierXML($id_classe) {
    global $pdo;
    
    try {
        // Récupération des informations de la classe
        $stmt_classe = $pdo->prepare("
            SELECT c.ID_CLASSE, f.NOM_FILIERE, c.NIVEAU 
            FROM classes c 
            JOIN filieres f ON c.ID_FILIERE = f.ID_FILIERE 
            WHERE c.ID_CLASSE = ?
        ");
        $stmt_classe->execute([$id_classe]);
        $classe_info = $stmt_classe->fetch(PDO::FETCH_ASSOC);
        
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
        
        // Sauvegarde du fichier XML
        $filename = 'emplois_xml/emploi_' . $id_classe . '.xml';
        $xml->save($filename);
        return $filename;
        
    } catch(PDOException $e) {
        return false;
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tableau de Bord - Gestion des Emplois du Temps</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        .nav-link { color: #333; }
        .nav-link.active { background-color: #0d6efd !important; color: white !important; }
        .tab-pane { padding: 20px 0; }
        .loading { display: none; text-align: center; padding: 20px; }
        .card { margin-bottom: 20px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        .chart-container { position: relative; height: 400px; width: 100%; }
        #successMessage, #errorMessage { display: none; padding: 10px; margin: 10px 0; border-radius: 4px; }
        #successMessage { background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        #errorMessage { background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary mb-4">
        <div class="container">
            <a class="navbar-brand" href="#">Gestion des Emplois du Temps</a>
        </div>
    </nav>

    <div class="container">
        <div class="row">
            <div class="col-md-3">
                <div class="card">
                    <div class="card-header">Navigation</div>
                    <div class="nav flex-column nav-pills p-3" id="v-pills-tab" role="tablist">
                        <button class="nav-link active mb-2" data-bs-toggle="pill" data-bs-target="#etudiants" role="tab">
                            Étudiants et Modules
                        </button>
                        <button class="nav-link mb-2" data-bs-toggle="pill" data-bs-target="#charges" role="tab">
                            Charges Horaires
                        </button>
                        <button class="nav-link" data-bs-toggle="pill" data-bs-target="#ajouter-seance" role="tab">
                            Ajouter une Séance
                        </button>
                    </div>
                </div>
            </div>

            <div class="col-md-9">
                <div class="tab-content" id="v-pills-tabContent">
                    <!-- Étudiants et Modules -->
                    <div class="tab-pane fade show active" id="etudiants">
                        <div class="card">
                            <div class="card-header">
                                <h2 class="h5 mb-0">Étudiants et Modules</h2>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <label for="classe-details" class="form-label">Sélectionnez une classe :</label>
                                    <select class="form-select" id="classe-details">
                                        <option value="">Choisir une classe...</option>
                                        <?php
                                        try {
                                            $stmt = $pdo->query("SELECT c.ID_CLASSE as id, CONCAT(f.NOM_FILIERE, ' - Niveau ', c.NIVEAU) as nom FROM classes c JOIN filieres f ON c.ID_FILIERE = f.ID_FILIERE ORDER BY f.NOM_FILIERE, c.NIVEAU");
                                            while($row = $stmt->fetch()) {
                                                echo "<option value='" . $row['id'] . "'>" . htmlspecialchars($row['nom']) . "</option>";
                                            }
                                        } catch(PDOException $e) {
                                            echo "<option value=''>Erreur de chargement</option>";
                                        }
                                        ?>
                                    </select>
                                </div>
                                <div id="classe-details-content"></div>
                            </div>
                        </div>
                    </div>

                    <!-- Charges Horaires -->
                    <div class="tab-pane fade" id="charges">
                        <div class="card">
                            <div class="card-header">
                                <h2 class="h5 mb-0">Charges Horaires des Enseignants</h2>
                            </div>
                            <div class="card-body">
                                <div class="chart-container">
                                    <canvas id="chargesChart"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Ajouter une Séance -->
                    <div class="tab-pane fade" id="ajouter-seance">
                        <div class="card">
                            <div class="card-header">
                                <h2 class="h5 mb-0">Ajouter une nouvelle séance</h2>
                            </div>
                            <div class="card-body">
                                <div id="successMessage"></div>
                                <div id="errorMessage"></div>
                                <form id="seanceForm" method="post" action="ajouter_seance.php">
                                    <div class="mb-3">
                                        <label for="classe" class="form-label">Classe</label>
                                        <select class="form-select" id="classe" name="classe" required>
                                            <option value="">Sélectionner une classe...</option>
                                            <?php
                                            try {
                                                $stmt = $pdo->query("SELECT c.ID_CLASSE as id, CONCAT(f.NOM_FILIERE, ' - Niveau ', c.NIVEAU) as nom FROM classes c JOIN filieres f ON c.ID_FILIERE = f.ID_FILIERE ORDER BY f.NOM_FILIERE, c.NIVEAU");
                                                while($row = $stmt->fetch()) {
                                                    echo "<option value='" . $row['id'] . "'>" . htmlspecialchars($row['nom']) . "</option>";
                                                }
                                            } catch(PDOException $e) {
                                                echo "<option value=''>Erreur de chargement</option>";
                                            }
                                            ?>
                                        </select>
                                    </div>
                                    <div class="mb-3">
                                        <label for="professeur" class="form-label">Professeur</label>
                                        <select class="form-select" id="professeur" name="professeur" required>
                                            <option value="">Sélectionner un professeur...</option>
                                            <?php
                                            try {
                                                $stmt = $pdo->query("SELECT ID_PROF as id, NOM_PROF as nom FROM professeurs ORDER BY NOM_PROF");
                                                while($row = $stmt->fetch()) {
                                                    echo "<option value='" . $row['id'] . "'>" . htmlspecialchars($row['nom']) . "</option>";
                                                }
                                            } catch(PDOException $e) {
                                                echo "<option value=''>Erreur de chargement</option>";
                                            }
                                            ?>
                                        </select>
                                    </div>
                                    <div class="mb-3">
                                        <label for="module" class="form-label">Module</label>
                                        <select class="form-select" id="module" name="module" required>
                                            <option value="">Sélectionner un module...</option>
                                            <?php
                                            try {
                                                $stmt = $pdo->query("SELECT ID_MODULE as id, CONCAT(ID_MODULE, ' - ', NOM_MODULE) as nom FROM modules ORDER BY ID_MODULE");
                                                while($row = $stmt->fetch()) {
                                                    echo "<option value='" . $row['id'] . "'>" . htmlspecialchars($row['nom']) . "</option>";
                                                }
                                            } catch(PDOException $e) {
                                                echo "<option value=''>Erreur de chargement</option>";
                                            }
                                            ?>
                                        </select>
                                    </div>
                                    <div class="mb-3">
                                        <label for="salle" class="form-label">Salle</label>
                                        <select class="form-select" id="salle" name="salle" required>
                                            <option value="">Sélectionner une salle...</option>
                                            <?php
                                            try {
                                                $stmt = $pdo->query("SELECT ID_SALLE as id, NOM_SALLE as nom FROM salles ORDER BY NOM_SALLE");
                                                while($row = $stmt->fetch()) {
                                                    echo "<option value='" . $row['id'] . "'>" . htmlspecialchars($row['nom']) . "</option>";
                                                }
                                            } catch(PDOException $e) {
                                                echo "<option value=''>Erreur de chargement</option>";
                                            }
                                            ?>
                                        </select>
                                    </div>
                                    <div class="mb-3">
                                        <label for="jour" class="form-label">Jour</label>
                                        <select class="form-select" id="jour" name="jour" required>
                                            <option value="">Sélectionner un jour...</option>
                                            <option value="Lundi">Lundi</option>
                                            <option value="Mardi">Mardi</option>
                                            <option value="Mercredi">Mercredi</option>
                                            <option value="Jeudi">Jeudi</option>
                                            <option value="Vendredi">Vendredi</option>
                                            <option value="Samedi">Samedi</option>
                                        </select>
                                    </div>
                                    <div class="mb-3">
                                        <label for="heure_debut" class="form-label">Heure de début</label>
                                        <input type="time" class="form-control" id="heure_debut" name="heure_debut" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="heure_fin" class="form-label">Heure de fin</label>
                                        <input type="time" class="form-control" id="heure_fin" name="heure_fin" required>
                                    </div>
                                    <button type="submit" class="btn btn-primary">Ajouter la séance</button>
                                </form>
                            </div>
                        </div>

                        <!-- Emploi du temps sous le formulaire -->
                        <div class="card mt-4">
                            <div class="card-header">
                                <h2 class="h5 mb-0">Emploi du Temps</h2>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <label for="classe-emploi-seance" class="form-label">Sélectionnez une classe :</label>
                                    <select class="form-select" id="classe-emploi-seance">
                                        <option value="">Choisir une classe...</option>
                                        <?php
                                        try {
                                            $stmt = $pdo->query("SELECT c.ID_CLASSE as id, CONCAT(f.NOM_FILIERE, ' - Niveau ', c.NIVEAU) as nom FROM classes c JOIN filieres f ON c.ID_FILIERE = f.ID_FILIERE ORDER BY f.NOM_FILIERE, c.NIVEAU");
                                            while($row = $stmt->fetch()) {
                                                echo "<option value='" . $row['id'] . "'>" . htmlspecialchars($row['nom']) . "</option>";
                                            }
                                        } catch(PDOException $e) {
                                            echo "<option value=''>Erreur de chargement</option>";
                                        }
                                        ?>
                                    </select>
                                </div>
                                <div id="emploi-temps-seance"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        let chargesChart = null; // Variable pour stocker l'instance du graphique

        // Fonction pour charger le graphique des charges horaires
        function chargerGraphique() {
            fetch("get_charges_horaires.php")
                .then(response => response.json())
                .then(data => {
                    // Si un graphique existe déjà, on le détruit
                    if (chargesChart) {
                        chargesChart.destroy();
                    }

                    const ctx = document.getElementById('chargesChart').getContext('2d');
                    chargesChart = new Chart(ctx, {
                        type: 'bar',
                        data: {
                            labels: data.labels,
                            datasets: [{
                                label: 'Heures par semaine',
                                data: data.heures,
                                backgroundColor: 'rgba(54, 162, 235, 0.8)',
                                borderColor: 'rgba(54, 162, 235, 1)',
                                borderWidth: 1
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            scales: {
                                y: {
                                    beginAtZero: true,
                                    title: {
                                        display: true,
                                        text: 'Heures'
                                    }
                                }
                            },
                            plugins: {
                                legend: {
                                    display: true,
                                    position: 'top'
                                },
                                title: {
                                    display: true,
                                    text: 'Charges horaires des enseignants'
                                }
                            }
                        }
                    });
                })
                .catch(error => {
                    console.error('Erreur lors du chargement des charges horaires:', error);
                    document.getElementById('chargesChart').innerHTML = 
                        '<div class="alert alert-danger">Erreur lors du chargement des charges horaires.</div>';
                });
        }

        // Charger le graphique lors du changement d'onglet
        document.querySelectorAll('.nav-link').forEach(tab => {
            tab.addEventListener('click', function() {
                if (this.getAttribute('data-bs-target') === '#charges') {
                    chargerGraphique();
                }
            });
        });

        // Charger le graphique au chargement initial si on est sur l'onglet charges
        if (document.querySelector('.nav-link[data-bs-target="#charges"]').classList.contains('active')) {
            chargerGraphique();
        }

        // Charger les listes de classes
        function chargerClasses() {
            fetch("get_listes.php?type=classes")
                .then(response => response.json())
                .then(classes => {
                    ['classe-details', 'classe-emploi-seance'].forEach(id => {
                        const select = document.getElementById(id);
                        select.innerHTML = '<option value="">Choisir une classe...</option>';
                        classes.forEach(classe => {
                            const option = document.createElement('option');
                            option.value = classe.id;
                            option.textContent = classe.nom;
                            select.appendChild(option);
                        });
                    });
                });
        }

        // Charger les détails de la classe
        document.getElementById('classe-details').addEventListener('change', function() {
            if (!this.value) return;
            fetch("transformer_classe.php?classe=" + this.value)
                .then(response => response.text())
                .then(html => {
                    document.getElementById('classe-details-content').innerHTML = html;
                });
        });

        // Gestion du formulaire d'ajout de séance
        const seanceForm = document.getElementById('seanceForm');
        if (seanceForm) {
            seanceForm.addEventListener('submit', function(e) {
                e.preventDefault();
                const formData = new FormData(this);

                fetch('ajouter_seance.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Générer le fichier XML après l'ajout réussi
                        fetch('generer_emploi.php?classe=' + formData.get('classe'))
                        .then(response => response.json())
                        .then(xmlData => {
                            if (xmlData.success) {
                                document.getElementById('successMessage').style.display = 'block';
                                document.getElementById('successMessage').textContent = 
                                    'Séance ajoutée et fichier XML généré avec succès';
                                
                                // Recharger l'emploi du temps
                                const classeEmploiSeance = document.getElementById('classe-emploi-seance');
                                if (classeEmploiSeance.value) {
                                    fetch("transformer_emploi.php?classe=" + classeEmploiSeance.value)
                                        .then(response => response.text())
                                        .then(html => {
                                            document.getElementById('emploi-temps-seance').innerHTML = html;
                                        });
                                }
                                
                                // Réinitialiser le formulaire
                                this.reset();
                            }
                        });
                    } else {
                        document.getElementById('errorMessage').style.display = 'block';
                        document.getElementById('errorMessage').textContent = data.message;
                    }
                });
            });
        }

        // Charger l'emploi du temps dans l'onglet ajout séance
        document.getElementById('classe-emploi-seance').addEventListener('change', function() {
            if (!this.value) return;
            fetch("transformer_emploi.php?classe=" + this.value)
                .then(response => response.text())
                .then(html => {
                    document.getElementById('emploi-temps-seance').innerHTML = html;
                });
        });
    });
    </script>
</body>
</html> 
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Détails de la Classe</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .loading {
            display: none;
            text-align: center;
            padding: 20px;
        }
    </style>
</head>
<body>
    <div class="container py-4">
        <h1 class="text-center mb-4">Informations de la Classe</h1>
        
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body">
                        <form id="classeForm" class="mb-4">
                            <div class="mb-3">
                                <label for="classe" class="form-label">Sélectionnez une classe :</label>
                                <select class="form-select" id="classe" name="classe" required>
                                    <option value="">Choisir une classe...</option>
                                    <?php
                                    try {
                                        $pdo = new PDO("mysql:host=localhost;dbname=gestion_emploi_temps", "root", "");
                                        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                                        
                                        $stmt = $pdo->query("
                                            SELECT 
                                                c.ID_CLASSE as id,
                                                CONCAT(f.NOM_FILIERE, ' - Niveau ', c.NIVEAU) as nom
                                            FROM classes c
                                            JOIN filieres f ON c.ID_FILIERE = f.ID_FILIERE
                                            ORDER BY f.NOM_FILIERE, c.NIVEAU
                                        ");
                                        
                                        while ($row = $stmt->fetch()) {
                                            echo "<option value='" . htmlspecialchars($row['id']) . "'>" . 
                                                 htmlspecialchars($row['nom']) . "</option>";
                                        }
                                    } catch(PDOException $e) {
                                        echo "<option value=''>Erreur lors du chargement des classes</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        
        <div id="loading" class="loading">
            Chargement en cours...
        </div>
        
        <div id="resultat"></div>
    </div>

    <script>
        document.getElementById('classe').addEventListener('change', function() {
            const classeId = this.value;
            if (!classeId) return;
            
            const loading = document.getElementById('loading');
            const resultat = document.getElementById('resultat');
            
            loading.style.display = 'block';
            resultat.innerHTML = '';
            
            // Charger le XML et appliquer la transformation XSLT
            fetch('transformer_classe.php?classe=' + classeId)
                .then(response => response.text())
                .then(html => {
                    loading.style.display = 'none';
                    resultat.innerHTML = html;
                })
                .catch(error => {
                    loading.style.display = 'none';
                    resultat.innerHTML = '<div class="alert alert-danger">Erreur lors du chargement des données</div>';
                });
        });
    </script>
</body>
</html> 
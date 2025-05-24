USE gestion_emploi_temps;

-- Insertion des filières
INSERT INTO filieres (NOM_FILIERE, DESCRITION) VALUES
('Informatique', 'Formation en développement et systèmes informatiques'),
('Gestion', 'Formation en management et comptabilité'),
('Marketing', 'Formation en marketing et communication');

-- Insertion des classes
INSERT INTO classes (ID_FILIERE, NIVEAU) VALUES
(1, 1), -- Informatique 1ère année
(1, 2), -- Informatique 2ème année
(2, 1), -- Gestion 1ère année
(2, 2), -- Gestion 2ème année
(3, 1); -- Marketing 1ère année

-- Insertion des étudiants
INSERT INTO etudiants (NUM_INSCRIPTION, NOM_ET, PRENOM_ET, ADRESSE, ID_CLASSE) VALUES
('2025-INF-001', 'DUPONT', 'Jean', '123 Rue de Paris', 1),
('2025-INF-002', 'MARTIN', 'Marie', '456 Avenue des Champs', 1),
('2025-GES-001', 'DUBOIS', 'Pierre', '789 Boulevard Central', 3),
('2025-MKT-001', 'LEFEBVRE', 'Sophie', '321 Rue du Commerce', 5);

-- Insertion des salles
INSERT INTO salles (NOM_SALLE, DESCIPTION) VALUES
('Salle 101', 'Salle informatique - 30 postes'),
('Salle 102', 'Salle de cours standard'),
('Salle 201', 'Amphithéâtre - 100 places'),
('Salle 202', 'Laboratoire informatique');

-- Insertion des modules
INSERT INTO modules (ID_MODULE, NOM_MODULE, DESCRIPTION) VALUES
('INFO101', 'Programmation Java', 'Introduction à la programmation Java'),
('INFO102', 'Base de données', 'Conception et gestion des bases de données'),
('GES101', 'Comptabilité', 'Principes fondamentaux de la comptabilité'),
('MKT101', 'Marketing digital', 'Stratégies de marketing numérique');

-- Insertion des professeurs
INSERT INTO professeurs (NOM_PROF, TEL) VALUES
('Dr. BERNARD', '0123456789'),
('Prof. PETIT', '0234567890'),
('M. ROBERT', '0345678901'),
('Mme. RICHARD', '0456789012');

-- Insertion des cours
INSERT INTO cours (ID_CLASSE, ID_PROF, ID_SALLE, ID_MODULE, JOUR, HEURE_DEBUT, HEURE_FIN) VALUES
(1, 1, 1, 'INFO101', 'Lundi', '08:00:00', '10:00:00'),
(1, 2, 2, 'INFO102', 'Lundi', '10:15:00', '12:15:00'),
(3, 3, 3, 'GES101', 'Mardi', '09:00:00', '11:00:00'),
(5, 4, 2, 'MKT101', 'Mercredi', '14:00:00', '16:00:00'); 
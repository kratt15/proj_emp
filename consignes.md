# Travaux Pratique sur AJAX

On souhaite créer une application web moderne pour gérer les emplois du temps d'un établissement scolaire.

L'établissement est composé de :

- Des **étudiants** regroupés en **classes**
- Chaque **classe** appartient à une **filière**
- Des **enseignants** dispensent des **modules** à des **classes** dans des **salles**, à une **date**, avec une **heure de début** et une **heure de fin**

## Schéma relationnel

### Etudiants

| Champ           | Type        |
| --------------- | ----------- |
| NUM_INSCRIPTION | varchar(15) |
| NOM_ET          | varchar(25) |
| PRENOM_ET       | varchar(25) |
| ADRESSE         | varchar(70) |

### Filières

| Champ       | Type        |
| ----------- | ----------- |
| ID_FILIERE  | int(11)     |
| NOM_FILIERE | varchar(15) |
| DESCRITION  | text        |

### Salles

| Champ      | Type        |
| ---------- | ----------- |
| ID_SALLE   | int(11)     |
| NOM_SALLE  | varchar(25) |
| DESCIPTION | text        |

### Classes

| Champ      | Type    |
| ---------- | ------- |
| ID_CLASSE  | int(11) |
| ID_FILIERE | int(11) |
| NIVEAU     | int(11) |

### Modules

| Champ       | Type        |
| ----------- | ----------- |
| ID_MODULE   | varchar(15) |
| NOM_MODULE  | varchar(25) |
| DESCRIPTION | text        |

### Professeurs

| Champ    | Type        |
| -------- | ----------- |
| ID_PROF  | int(11)     |
| NOM_PROF | varchar(25) |
| TEL      | varchar(12) |

### Cours

| Champ       | Type        |
| ----------- | ----------- |
| ID          | int(11)     |
| ID_CLASSE   | int(11)     |
| ID_PROF     | int(11)     |
| ID_SALLE    | int(11)     |
| ID_MODULE   | int(11)     |
| JOUR        | varchar(12) |
| HEURE_DEBUT | time        |
| HEURE_FIN   | time        |

## Travail demandé

### 1. Conception de la base de données

- Concevoir et implémenter une base de données relationnelle avec les tables suivantes :
  - etudiants, classes, filieres, enseignants, modules, seances, salles.
- Renseigner cette base avec des données réelles issues de l'emploi du temps actuel de votre établissement.

### 2. Génération d'un fichier XML représentant un emploi du temps

- Écrire un script PHP qui extrait l'emploi du temps d'une classe donnée depuis la base de données et le convertit en un fichier XML de la forme :

```xml
<?xml version="1.0" encoding="UTF-8"?>
<emploi classe="AL3">
    <seance jour="lundi" debut="08:30" fin="10:00" prof="A" module="M1" salle="lab4"/>
    <seance jour="lundi" debut="10:15" fin="11:45" prof="B" module="M2" salle="londres"/>
</emploi>
```

### 3. Interface Ajax de consultation de l'emploi du temps

- Créer une page HTML avec JavaScript (Ajax) et PHP qui permet :
  - De sélectionner une classe via une liste déroulante
  - D'afficher dynamiquement l'emploi du temps (formaté à partir du fichier XML ou généré en HTML par PHP)

### 4. Transformation XSL vers HTML5

- Écrire une feuille de style XSLT qui transforme le fichier XML d'emploi du temps en une page HTML responsive, structurée et stylisée (ex. avec Bootstrap).

### 5. Visualisation graphique des charges horaires

- Créer une page qui génère un graphique SVG ou utilise une bibliothèque moderne (comme Chart.js ou D3.js) pour représenter :
  - Le nombre total d'heures enseignées par chaque enseignant sur une semaine.

### 6. Représentation XML de la liste des étudiants

- Écrire un fichier XML représentant la structure d'une classe, ses étudiants et les modules enseignés :

```xml
<classe filiere="SRI" niveau="5">
    <etudiants>
        <etudiant numInscription="E200" nom="X" prenom="Y"/>
    </etudiants>
    <modules>
        <module idModule="M1" nomModule="Java"/>
    </modules>
</classe>
```

### 7. Génération dynamique du XML des étudiants

- Écrire un script PHP qui prend l'ID d'une classe et génère dynamiquement le fichier XML précédent avec les données de la base.

### 8. Affichage des étudiants et modules d'une classe

- Écrire une feuille XSLT pour transformer le fichier XML en tableau HTML listant les étudiants et les modules.
- Créer une page HTML+PHP qui permet :
  - De sélectionner une classe
  - D'afficher dans la même page la liste des étudiants et des modules.

### 9. Saisie d'un nouvel emploi du temps (CRUD simplifié)

- Créer une page web avec formulaire dynamique (AJAX + PHP) permettant :
  - La saisie d'une nouvelle séance dans un emploi du temps
  - L'envoi des données via Ajax
  - La mise à jour de la base et éventuellement du fichier XML.

### 10. Intégration dans une page principale (Dashboard)

- Créer une interface principale intégrant :
  - La visualisation d'un emploi du temps
  - La consultation des étudiants et modules
  - Le graphique des heures par professeur
  - La saisie des séances.
- Utiliser un design responsif et accessible (ex. Bootstrap ou CSS Grid/Flexbox).

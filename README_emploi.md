# Générateur d'Emploi du Temps XML

Ce script PHP permet de générer un fichier XML représentant l'emploi du temps d'une classe donnée.

## Prérequis

- Un serveur web avec PHP 7.0 ou supérieur
- MySQL Server
- PDO PHP Extension
- La base de données créée avec les scripts SQL fournis

## Installation

1. Placez le fichier `generer_emploi.php` dans votre répertoire web
2. Assurez-vous que la base de données est créée et peuplée avec les données
3. Modifiez si nécessaire les paramètres de connexion à la base de données dans le script :
   - `$host`
   - `$dbname`
   - `$username`
   - `$password`

## Utilisation

Pour générer l'emploi du temps d'une classe, accédez au script via votre navigateur :

```
http://votre-serveur/generer_emploi.php?classe=ID_CLASSE
```

Où `ID_CLASSE` est l'identifiant de la classe dont vous souhaitez obtenir l'emploi du temps.

Par exemple :

```
http://localhost/generer_emploi.php?classe=1
```

## Format de Sortie

Le script génère un fichier XML avec la structure suivante :

```xml
<?xml version="1.0" encoding="UTF-8"?>
<emploi classe="NomFilièreNiveau">
    <seance jour="jour" debut="HH:MM" fin="HH:MM" prof="NomProf" module="CodeModule" salle="NomSalle"/>
    ...
</emploi>
```

## Gestion des Erreurs

En cas d'erreur (problème de connexion à la base de données, classe non trouvée, etc.), le script génère un XML d'erreur :

```xml
<?xml version="1.0" encoding="UTF-8"?>
<erreur>Description de l'erreur</erreur>
```

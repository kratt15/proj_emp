# Gestion des Emplois du Temps - Base de Données

Ce projet contient les scripts SQL nécessaires pour créer et peupler une base de données de gestion des emplois du temps d'un établissement scolaire.

## Structure des Fichiers

- `create_database.sql` : Script de création de la structure de la base de données
- `insert_data.sql` : Script d'insertion des données d'exemple
- `README.md` : Ce fichier de documentation

## Prérequis

- MySQL Server
- Un client MySQL (MySQL Workbench, phpMyAdmin, etc.)

## Installation

1. Connectez-vous à votre serveur MySQL
2. Exécutez d'abord le script `create_database.sql` pour créer la base de données et ses tables
3. Exécutez ensuite le script `insert_data.sql` pour insérer les données d'exemple

## Structure de la Base de Données

La base de données contient les tables suivantes :

- `filieres` : Les différentes filières de l'établissement
- `classes` : Les classes, liées aux filières
- `etudiants` : Les informations des étudiants
- `salles` : Les salles disponibles
- `modules` : Les modules enseignés
- `professeurs` : Les informations des enseignants
- `cours` : Les séances de cours programmées

## Remarques

- Les données d'exemple fournies dans `insert_data.sql` peuvent être modifiées selon vos besoins
- Tous les champs obligatoires sont marqués comme `NOT NULL` dans la structure
- Des clés étrangères sont utilisées pour maintenir l'intégrité référentielle

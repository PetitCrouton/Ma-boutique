<?php
$pdo = new PDO('mysql:host=localhost;dbname=wf3_site', 'root', 'root', array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING, PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8'));

// Appel du fichier avec toutes nos fonctions
require_once("function.inc.php");

// Création de variables pouvant nous servir dans le cadre du projet : 
// Variable pour afficher des messages à l'utilisateur
$message = "";

// ouverture de la session
session_start();

// définition de constante pour le chemin absolu ainsi que pour la racine serveur
define("URL", "/FORMATION/PHP/site/");

// racine serveur
define("RACINE_SERVEUR", $_SERVER['DOCUMENT_ROOT'] . URL );
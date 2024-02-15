<?php

abstract class Database {
//Stock l'instance unique de PDO
    private static $pdo;

//Génère la connexion à la base de donnée
    private static function setBdd() {
        try {

            self::$pdo = new PDO("mysql:host=localhost;dbname=soundsphere;charset=utf8", "root", "");
            self::$pdo->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_WARNING);

        } catch(PDOException $e) {
//On enregistre l'erreur dans un fichier de log
            error_log("Erreur de base de donnée: " . $e->getMessage() . " dans " . $e->getFile() . ' ligne ' . $e->getLine());
//On affiche un message à l'utilisateur
            throw new Exception(("Erreur de connexion à la base de donnée."));

        }
    }

//Renvoi l'instance de PDO si l'instance n'est pas initialisé elle appel setBdd() pour le faire
    protected function getBdd(){
        if(self::$pdo === null){
            self::setBdd();
        }
        return self::$pdo;
        
    }

    //gestion des exceptions
    protected function handleException(PDOException $e, $action = "effectuer l'opération") {
        $errorMsg = "Erreur lors de la tentative de: " . $action . ": "
        . "Fichier: " . $e->getFile() 
        . " à la ligne " . $e->getLine()
        . ". Erreur: " . $e->getMessage();        
        error_log($errorMsg);

        throw new Exception("Une erreur est survenue, veuillez réessayer plus tard.");
    }
}
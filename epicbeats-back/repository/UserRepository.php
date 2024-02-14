<?php

require_once './soundsphere-back/models/Database.php';
require_once './soundsphere-back/models/Users.php';


class UserRepository extends Database {

    /**
      * Vérifie si un utilisateur existe dans la base de données en utilisant son ID.
      *
      * Cette méthode prépare et exécute une requête SQL pour compter le nombre d'entrées correspondant
      * à l'ID d'utilisateur fourni. Elle retourne vrai (true) si l'utilisateur existe (au moins une entrée),
      * ou faux (false) dans le cas contraire.
      *
      * @param int $idUser L'ID de l'utilisateur à rechercher dans la base de données.
      * @return bool Retourne true si l'utilisateur existe, false sinon.
      *
      * @throws Exception Si une erreur de connexion à la base de données se produit,
      * cette méthode propage une exception avec un message d'erreur adapté.
    */
    public function doesUserExist (int $idUser) :bool  {
        try {
            $db = $this->getBdd();
            $req = "SELECT COUNT(*) FROM users WHERE id = :idUser";

            $stmt = $db->prepare($req);
            $stmt->bindValue(":idUser", $idUser, PDO::PARAM_INT);
            $stmt->execute();

            $count = $stmt->fetchColumn();
            return $count > 0;

        } catch (PDOException $e) {
            $this->handleException($e, "recherche de l'utilisateur");
        }
    }




}
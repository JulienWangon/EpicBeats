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


    /**
      * Récupère les informations d'un utilisateur spécifique par son ID.
      *
      * Cette méthode exécute une requête SQL pour récupérer les informations d'un utilisateur, y compris son rôle,
      * en utilisant son ID unique. Les informations retournées incluent l'ID de l'utilisateur, son nom d'utilisateur,
      * son adresse email, et le nom de son rôle. Si aucun utilisateur n'est trouvé avec l'ID fourni, la méthode retourne null.
      *
      * @param int $userId L'ID de l'utilisateur à rechercher.
      * @return array|null Un tableau associatif contenant les informations de l'utilisateur si trouvé, null sinon.
      *
      * @throws Exception Si une erreur de connexion à la base de données se produit,
      * cette méthode propage une exception avec un message d'erreur adapté.
    */
    public function getUserByID($userId) {          
        try {
            $db = $this->getBdd();
            $req = "SELECT u.id, u.userName, u.email, u.idRole, r.name
                    FROM users u 
                    JOIN roles r
                    ON u.idRole = r.id
                    WHERE u.id = :userId";

            $stmt = $db->prepare($req);
            $stmt->bindValue(":userId", $userId, PDO::PARAM_INT);
            $stmt->execute();

            $userData = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($userData) {      
                $adjustedUserData = [
                    "id" => $userData['id'],
                    "userName" => $userData['userName'],
                    "email" => $userData['email'],
                    "roleName" => $userData['name'],
                ];

                return $adjustedUserData;
            } else {
                return null;
            }
        } catch (PDOException $e) {
            $this->handleException($e, "recherche de l'utilisateur.");
        }   
    }


    /**
      * Vérifie si un email est déjà utilisé par un utilisateur dans la base de données.
      *
      * Cette méthode exécute une requête SQL pour compter le nombre d'utilisateurs ayant l'email spécifié.
      * Il est possible d'exclure un utilisateur spécifique de cette vérification grâce à son ID. Cela est utile,
      * par exemple, lors de la mise à jour des informations d'un utilisateur, pour s'assurer que l'email proposé
      * n'est utilisé que par cet utilisateur. Si aucun utilisateur n'est trouvé avec l'email spécifié (ou si l'email
      * est trouvé mais appartient à l'utilisateur exclu), la méthode retourne false. Sinon, elle retourne true,
      * indiquant que l'email est déjà utilisé.
      *
      * @param string $email L'email à vérifier dans la base de données.
      * @param int|null $excludeUserId (Optionnel) L'ID d'un utilisateur à exclure de la vérification.
      * @return bool True si l'email est déjà utilisé par un autre utilisateur, false sinon.
      *
      * @throws Exception Si une erreur de connexion à la base de données se produit,
      * cette méthode propage une exception avec un message d'erreur adapté.
    */
    public function doesEmailExist(string $email, int $excludeUserId = null) : bool {
        try {
            $db = $this->getBdd();
            $req = "SELECT COUNT(1) FROM users WHERE email = :email";

            if ($excludeUserId !== null) {
                $req .= " AND id != :excludeUserId";
            }

            $stmt = $db->prepare($req);

            $stmt->bindValue(":email", $email, PDO::PARAM_STR);

            if ($excludeUserId !== null) {
                $stmt->bindValue(":excludeUserId", $excludeUserId, PDO::PARAM_INT);
            }
            $stmt->execute();
            
            return $stmt->fetchColumn() > 0;
            
        } catch (PDOException $e) {
            $this->handleException($e, "vérification de l'existence de l'email");
            return false;
        }
    }





}
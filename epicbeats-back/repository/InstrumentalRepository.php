<?php

require_once './soundsphere-back/models/Database.php';
require_once './soundsphere-back/models/Instrumental.php';


class InstrumentalRepository extends Database {

    /**
      * Récupère tous les enregistrements d'instrumentaux depuis la base de données.
      *
      * Cette méthode exécute une requête SQL pour sélectionner tous les champs de tous les enregistrements
      * dans la table 'instrumental'. Chaque enregistrement est utilisé pour créer une instance de la classe
      * Instrumental, qui est ensuite ajoutée à un tableau. Ce tableau, contenant des instances de tous les
      * instrumentaux trouvés dans la base de données, est retourné à l'appelant. Si une erreur survient lors
      * de l'exécution de la requête ou pendant le processus de récupération des données, la méthode gère
      * l'exception et enregistre l'erreur.
      *
      * @return array Un tableau d'instances d'Instrumental représentant chaque instrumental trouvé dans la base de données.
      *
      * @throws Exception Propage une exception si une erreur de connexion à la base de données se produit,
      * en fournissant un message d'erreur approprié pour le débogage. La méthode utilise `handleException`
      * pour gérer l'exception et enregistrer les détails de l'erreur.
    */
    public function getAllInstrumental() :array {
      try {
          $db=$this->getBdd();
          $req = "SELECT * FROM instrumental";

          $stmt = $db->prepare($req);
          $stmt->execute();
          $instrumentalsData = $stmt->fetchAll(PDO::FETCH_ASSOC); 

          $instrumentals = [];
          foreach ($instrumentalsData as $instrumentalData) {

            $instrumental = new Instrumental(  
                $instrumentalData['title'],
                $instrumentalData['gender'],
                $instrumentalData['bpm'],
                $instrumentalData['cover'],
                $instrumentalData['soundPath'],
                $instrumentalData['price'],
                $instrumentalData['id'] ?? null
            );

            $instrumentals[] = $instrumental;     
        }

        return $instrumentals;

      } catch(PDOException $e){
          $this->handleException($e, "extraction de tous les avis clients");
      }
    }
}
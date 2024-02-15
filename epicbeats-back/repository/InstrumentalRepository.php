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


    /**
      * Obtient une liste filtrée d'instrumentales basée sur des critères spécifiques.
      *
      * Cette méthode exécute une requête SQL sur la table 'instrumental' pour récupérer des enregistrements
      * qui correspondent à un ensemble de filtres fournis. Les filtres peuvent inclure le genre, un BPM exact,
      * une plage de BPM (min et max), ainsi qu'une plage de prix (min et max). Les résultats sont ensuite
      * transformés en instances de la classe Instrumental, et un tableau de ces instances est retourné.
      * Si une erreur survient lors de l'exécution de la requête ou pendant le processus de récupération des données,
      * la méthode gère l'exception et enregistre l'erreur.
      *
      * @param array $filters Un tableau associatif contenant les critères de filtrage tels que 'genre', 'bpmExact',
      *                       'bpmMin', 'bpmMax', 'priceMin', et 'priceMax'.
      * @return array Un tableau d'instances d'Instrumental correspondant aux critères de filtrage fournis.
      *
      * @throws Exception Propage une exception si une erreur de connexion à la base de données se produit,
      * en fournissant un message d'erreur approprié pour le débogage. La méthode utilise `handleException`
      * pour gérer l'exception et enregistrer les détails de l'erreur.
    */
    public function getFilteredInstrumentals($filters) {
        try {
            $db = $this->getBdd();
            $req = "SELECT * FROM instrumental WHERE 1=1";

            if (!empty($filters['genre'])) {
                $req .= " AND gender = :genre";
            }
            if (!empty($filters['bpmExact'])) {
                $req .= " AND bpm = :bpmExact";
            } else {
                if (!empty($filters['bpmMin'])) {
                    $req .= " AND bpm >= :bpmMin";
                }
                if (!empty($filters['bpmMax'])) {
                    $req .= " AND bpm <= :bpmMax";
                }
            }
            if (!empty($filters['priceMin'])) {
                $req .= " AND price >= :priceMin";
            }
            if (!empty($filters['priceMax'])) {
                $req .= " AND price <= :priceMax";
            }

            $stmt = $db->prepare($req);


            if (!empty($filters['genre'])) {
                $stmt->bindValue(":genre", $filters['genre'], PDO::PARAM_STR);
            }
            if (!empty($filters['bpmExact'])) {
                $stmt->bindValue(":bpmExact", $filters['bpmExact'], PDO::PARAM_INT);
            }
            if (!empty($filters['bpmMin'])) {
                $stmt->bindValue(":bpmMin", $filters['bpmMin'], PDO::PARAM_INT);
            }
            if (!empty($filters['bpmMax'])) {
                $stmt->bindValue(":bpmMax", $filters['bpmMax'], PDO::PARAM_INT);
            }
            if (!empty($filters['priceMin'])) {
                $stmt->bindValue(":priceMin", $filters['priceMin'], PDO::PARAM_INT);
            }
            if (!empty($filters['priceMax'])) {
                $stmt->bindValue(":priceMax", $filters['priceMax'], PDO::PARAM_INT);
            }

            $stmt->execute();

            $filteredInstrumentalsData = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $instrumentals = [];
            foreach ($filteredInstrumentalsData as $filteredInstrumentalData) {
                $instrumental = new Instrumental(
                    $filteredInstrumentalData['title'],
                    $filteredInstrumentalData['gender'],
                    $filteredInstrumentalData['bpm'],
                    $filteredInstrumentalData['cover'],
                    $filteredInstrumentalData['soundPath'],
                    $filteredInstrumentalData['price'],
                    $filteredInstrumentalData['id'] ?? null
                );

                $instrumentals[] = $instrumental;
            }

            return $instrumentals; // Déplacez le return en dehors de la boucle

        } catch (PDOException $e) {
            $this->handleException($e, "Erreur lors de l'extraction des instrumentales filtrées.");
        }
    }
}
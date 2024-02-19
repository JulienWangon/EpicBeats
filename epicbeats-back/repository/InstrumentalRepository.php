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


    /**
      * Ajoute une nouvelle instrumentale dans la base de données.
      *
      * Cette méthode prend en paramètre une instance de la classe Instrumental contenant toutes les informations
      * nécessaires sur la nouvelle instrumentale à ajouter (titre, genre, BPM, chemin de la couverture, chemin du son,
      * et prix). Elle construit et exécute une requête SQL INSERT pour ajouter ces informations dans la table 'instrumental'
      * de la base de données. En cas de succès, l'ID de l'entrée insérée est retourné, permettant une référence ultérieure.
      *
      * @param Instrumental $instrumental Une instance de la classe Instrumental contenant les informations de l'instrumentale à ajouter.
      * @return int L'ID de l'instrumentale ajoutée dans la base de données.
      *
      * @throws Exception Propage une exception si une erreur de connexion à la base de données se produit ou si l'insertion échoue,
      * en fournissant un message d'erreur approprié pour le débogage. La méthode utilise `handleException`
      * pour gérer l'exception et enregistrer les détails de l'erreur.
    */
    public function addInstrumental(Instrumental $instrumental) :int {
        try {
            $db = $this->getBdd();
            $req = "INSERT INTO instrumental (title, gender, bpm, cover, soundPath, price) VALUES (:title, :gender, :bpm, :coverPath, :soundPath, :price)";
            $stmt = $db->prepare($req);

            $stmt->bindValue(":title", $instrumental->getTitle(), PDO::PARAM_STR);
            $stmt->bindValue(":gender", $instrumental->getGender(), PDO::PARAM_STR);
            $stmt->bindValue(":bpm", $instrumental->getBpm(), PDO::PARAM_INT);
            $stmt->bindValue(":coverPath", $instrumental->getCoverPath(), PDO::PARAM_STR);
            $stmt->bindValue(":soundPath", $instrumental->getSoundPath(), PDO::PARAM_STR);
            $stmt->bindValue(":price", $instrumental->getPrice(), PDO::PARAM_INT);

            $stmt->execute();

            $lastInsertId = $db->lastInsertId();
            return $lastInsertId;

        } catch (PDOException $e) {
            $this->handleException($e, "Enregistrement d'une nouvelle instrumentale.");
        }
    }


    /**
      * Supprime une instrumentale de la base de données.
      *
      * Cette méthode prend en paramètre une instance de la classe Instrumental, utilise son ID pour identifier
      * l'instrumentale à supprimer dans la table 'instrumental' de la base de données. La méthode construit et exécute
      * une requête SQL DELETE pour réaliser cette suppression. Si au moins une ligne est affectée par l'opération
      * (indiquant que l'instrumentale a été supprimée avec succès), la méthode retourne true. Sinon, elle retourne false,
      * indiquant qu'aucune ligne n'a été supprimée (par exemple, si l'ID fourni ne correspond à aucune instrumentale existante).
      *
      * @param Instrumental $instrumental Une instance de la classe Instrumental représentant l'instrumentale à supprimer.
      * @return bool True si l'opération de suppression a réussi et affecté au moins une ligne, False dans le cas contraire.
      *
      * @throws Exception Propage une exception si une erreur de connexion à la base de données se produit ou si la requête échoue,
      * en fournissant un message d'erreur approprié pour le débogage. La méthode utilise `handleException`
      * pour gérer l'exception et enregistrer les détails de l'erreur.
    */
    public function deleteInstrumental(Instrumental $instrumental) : bool {
        try {
            $db = $this->getBdd();
            $req = "DELETE FROM instrumental WHERE id = :id";
            
            $stmt = $db->prepare($req);
            $stmt->bindValue(":id", $instrumental->getId(), PDO::PARAM_INT);
            $stmt->execute();

            return $stmt->rowCount() > 0;

        } catch(PDOException $e) {

            $this->handleException($e, "suppression d'une instrumentale");
        }
    }


    /**
      * Met à jour les informations d'une instrumentale dans la base de données.
      *
      * Cette méthode prend une instance de la classe Instrumental contenant les informations mises à jour
      * de l'instrumentale (titre, genre, BPM, chemin de la couverture, chemin du fichier sonore, et prix)
      * et utilise son ID pour identifier l'instrumentale à mettre à jour dans la table 'instrumental'.
      * La méthode construit une requête SQL UPDATE en utilisant les valeurs fournies et exécute cette requête.
      * Si l'opération affecte au moins une ligne (indiquant que les informations de l'instrumentale ont été
      * mises à jour avec succès), la méthode retourne true. Sinon, elle retourne false, indiquant qu'aucune
      * ligne n'a été mise à jour (par exemple, si les nouvelles valeurs sont identiques aux anciennes).
      *
      * @param Instrumental $instrumental Une instance de la classe Instrumental contenant les informations mises à jour.
      * @return bool True si l'opération de mise à jour a réussi et a affecté au moins une ligne, False dans le cas contraire.
      *
      * @throws Exception Propage une exception si une erreur de connexion à la base de données se produit ou si la requête échoue,
      * en fournissant un message d'erreur approprié pour le débogage. La méthode utilise `handleException`
      * pour gérer l'exception et enregistrer les détails de l'erreur.
    */
    public function updateInstrumental(Instrumental $instrumental) : bool {
        try {
            $db = $this->getBdd();
            $columnTable = [
                "title = :title",
                "gender = :gender",
                "bpm = :bpm",
                "cover = :coverPath", 
                "soundPath = :soundPath",
                "price = :price",
                "id = :id"   
            ];
            
            $req = "UPDATE instrumental SET " . implode(", ", $columnTable) . " WHERE id = :id";
            $stmt = $db->prepare($req);

            $stmt->bindValue(":title", $instrumental->getTitle(), PDO::PARAM_STR);
            $stmt->bindValue(":gender", $instrumental->getGender(), PDO::PARAM_STR);
            $stmt->bindValue(":bpm", $instrumental->getBpm(), PDO::PARAM_INT);
            $stmt->bindValue(":coverPath", $instrumental->getCoverPath(), PDO::PARAM_STR);
            $stmt->bindValue(":soundPath", $instrumental->getSoundPath(), PDO::PARAM_STR);
            $stmt->bindValue(":price", $instrumental->getPrice(), PDO::PARAM_INT);
            $stmt->bindValue(":id", $instrumental->getId(), PDO::PARAM_INT);

            $stmt->execute();

            return $stmt->rowCount() > 0;

        } catch(PDOException $e) {
            $this->handleException($e, "mise à jour d'une instrumentale");
        }
    }


    /**
      * Retrouve une instrumentale par son ID et retourne une instance de celle-ci.
      *
      * Cette méthode recherche dans la base de données une instrumentale correspondant à l'ID fourni.
      * Si une correspondance est trouvée, elle crée et retourne une nouvelle instance de la classe Instrumental
      * avec les données récupérées. Cela inclut le titre, le genre, le BPM, le chemin de la couverture, le chemin
      * du fichier sonore, le prix, et l'ID de l'instrumentale. Si aucune instrumentale correspondante n'est trouvée,
      * ou si une erreur survient lors de la recherche, la méthode retourne null.
      *
      * @param int $id L'ID de l'instrumentale à rechercher dans la base de données.
      * @return Instrumental|null Une instance de Instrumental si une correspondance est trouvée, null sinon.
      *
      * @throws Exception Propage une exception si une erreur de connexion à la base de données se produit,
      * en fournissant un message d'erreur approprié pour le débogage. La méthode utilise `handleException`
      * pour gérer l'exception et enregistrer les détails de l'erreur.
    */
    public function findInstrumentalById($id): ?Instrumental {
        try {
            $db = $this->getBdd();
            $req = "SELECT * FROM instrumental WHERE id = :id";
            $stmt = $db->prepare($req);
            $stmt->bindValue(":id", $id, PDO::PARAM_INT);
            $stmt->execute();

            $data = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($data) {
                // Créez une instance de Instrumental en passant les valeurs individuellement
                return new Instrumental(
                    $data['title'],
                    $data['gender'],
                    $data['bpm'],
                    $data['cover'],
                    $data['soundPath'],
                    $data['price'],
                    $data['id'] ?? null // Passez l'ID comme dernier argument, en utilisant l'opérateur null coalescent au cas où 'id' n'est pas défini
                );
            } else {
                return null;
            }

        } catch (PDOException $e) {
            $this->handleException($e, "recherche d'une instrumentale par son id.");
            return null;
        }
    }
    
}
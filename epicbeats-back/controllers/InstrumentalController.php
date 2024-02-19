<?php

require_once './epicbeats-back/models/Instrumental.php';
require_once './epicbeats-back/classes/ResponseHelper.php';
require_once './epicbeats-back/repository/InstrumentalRepository.php';
require_once './epicbeats-back/config/config.php';

class InstrumentalController {

    private $instrumentalRepository;
    

    public function __construct(InstrumentalRepository $instrumentalRepository) {
        $this->instrumentalRepository = $instrumentalRepository;
    
    }


    /**
      * Récupère et envoie la liste des instrumentales stockées dans la base de données.
      *
      * Cette méthode interroge la base de données pour obtenir toutes les instrumentales disponibles
      * en utilisant la méthode `getAllInstrumental` de l'objet `instrumentalRepository`. Elle traite
      * ensuite chaque instrumentale pour échapper les caractères spéciaux dans les champs de texte
      * avant de les envoyer au client sous forme de réponse JSON. Si aucune instrumentale n'est trouvée,
      * une réponse avec un message d'erreur est envoyée. En cas de succès, une réponse contenant un tableau
      * des instrumentales est envoyée au client. Chaque instrumentale est sécurisée pour l'affichage en
      * échappant les caractères spéciaux pour prévenir les attaques XSS. En cas d'erreur lors de l'exécution,
      * une réponse d'erreur est également envoyée.
      *
      * @return void La méthode envoie directement une réponse au client et ne retourne donc rien.
      *
      * @throws Exception Propage une exception si une erreur survient lors de l'interrogation de la base de données
      * ou lors de la préparation de la réponse. La méthode utilise `ResponseHelper::sendResponse` pour envoyer
      * des réponses de succès ou d'erreur au client, en fonction du résultat de l'opération.
    */
    public function getInstrumentalsList() {
        try {

            $instrumentals = $this->instrumentalRepository->getAllInstrumental();

            if(empty($instrumentals)) {
                ResponseHelper::sendResponse(['staus' => 'error', 'message' => 'Aucunes instrumentales trouvées.']);
                return;
            }

            $instrumentalsArray = array_map(function ($instrumental) {
                return [  
                    'title' => htmlspecialchars($instrumental->getTitle(), ENT_QUOTES, 'UTF-8'),
                    'gender' => htmlspecialchars($instrumental->getGender(), ENT_QUOTES, 'UTF-8'),
                    'bpm' => $instrumental->getBpm(),
                    'coverPath' => htmlspecialchars(BASE_PATH . $instrumental->getCoverPath(), ENT_QUOTES, 'UTF-8'),
                    'soundPath' => htmlspecialchars(BASE_PATH . $instrumental->getSoundPath(), ENT_QUOTES, 'UTF-8'),
                    'price' => $instrumental->getPrice(),
                    'id' => $instrumental->getId(),     
                ];
          }, $instrumentals);
            ResponseHelper::sendResponse(['status' => 'success', 'data' => $instrumentalsArray]);
        } catch (Exception $e) {
            ResponseHelper::sendResponse(['status' => 'error', 'message' => $e->getMessage()], 400);
        }
  }
}
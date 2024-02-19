<?php

class ResponseHelper {
    /**
      * Envoie une réponse HTTP formatée en JSON au client.
      *
      * Cette méthode statique est conçue pour standardiser l'envoi de réponses depuis l'API vers le client.
      * Elle définit le type de contenu de la réponse à 'application/json', fixe le code de statut HTTP à la valeur
      * fournie (200 par défaut), et encode les données fournies en JSON avant de les retourner au client.
      * Cela facilite la création d'une API RESTful en assurant que toutes les réponses suivent un format cohérent.
      *
      * @param mixed $data Les données à envoyer dans la réponse. Peuvent être de n'importe quel type qui est valide
      *                    pour une conversion en JSON, y compris les tableaux et objets.
      * @param int $statusCode (Optionnel) Le code de statut HTTP à envoyer avec la réponse. Par défaut à 200.
      *
      * @return void Cette méthode ne retourne rien car elle envoie directement la réponse au client.
    */

    public static function sendResponse($data, $statusCode = 200) {
        header("Content-Type: application/json");
        http_response_code($statusCode);
        echo json_encode($data);
    }
}

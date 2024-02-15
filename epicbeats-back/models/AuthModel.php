<?php

require_once 'Database.php';
require_once './soundsphere-back/vendor/autoload.php';
require_once './soundsphere-back/Class/SecurityUtil.php';

use Firebase\JWT\ExpiredException;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Random\Engine\Secure;

class AuthModel extends Database {
    private const TOKEN_EXPIRY_SECONDS = 7200;

    /**
      * Crée un JSON Web Token (JWT) pour un utilisateur spécifié.
      *
      * Cette méthode génère un JWT contenant des informations sur l'utilisateur, y compris son ID, son nom d'utilisateur, le nom de son rôle
      * ainsi qu'un jeton CSRF pour une sécurité renforcée. Le token inclut également les timestamps de création (iat) et
      * d'expiration (exp), basés sur une durée de validité définie par TOKEN_EXPIRY_SECONDS. Le JWT est signé avec une clé
      * secrète stockée dans la variable d'environnement 'SECRET_KEY'. Une exception est levée si cette clé secrète n'est pas
      * définie, garantissant que le token ne peut être créé sans une signature valide.
      *
      * @param array $user Un tableau associatif contenant au moins 'id', 'userName' et 'roleName de l'utilisateur.
      * @return array Un tableau associatif contenant le JWT, sa date d'expiration et le jeton CSRF.
      *
      * @throws Exception Si la clé secrète nécessaire pour signer le JWT est introuvable.
    */
    public function createJWTForUser($user) {
      $secretKey = $_ENV['SECRET_KEY'];

      if(!$secretKey) {
          throw new Exception("clé secrète introuvable");
      }

      $issueAt = time();
      $expiryTime = $issueAt + self::TOKEN_EXPIRY_SECONDS;

      $csrfToken = SecurityUtil::generateSecureToken();
      $payload = [
        "iat" => $issueAt,
        "exp" => $expiryTime,
        "id" => $user['id'],
        "userName" => $user['userName'],
        'roleName' => ['roleName'],
        "csrfToken" => $csrfToken
      ];

      $jwt = JWT::encode($payload, $secretKey, "HS256");
      $jwtData = ["jwt" => $jwt, "exp" => $expiryTime, "csrfToken" => $csrfToken];
      return $jwtData;
    }


  /**
    * Décode un JSON Web Token (JWT) stocké dans un cookie.
    *
    * Cette méthode lit un JWT depuis un cookie nommé 'token', puis tente de le décoder en utilisant la clé secrète
    * définie dans la variable d'environnement 'SECRET_KEY'. Elle vérifie que le JWT décodé contient tous les champs
    * requis, notamment l'ID de l'utilisateur, son nom d'utilisateur, le nom de son rôle et un jeton CSRF. Si le token
    * est valide et contient toutes les informations nécessaires, ces dernières sont retournées sous forme d'un tableau
    * associatif. Des exceptions sont levées en cas d'absence du token dans le cookie, si le JWT est expiré ou si une
    * autre erreur survient lors du décodage.
    *
    * @return array Un tableau associatif contenant l'ID de l'utilisateur, son nom d'utilisateur, le nom de son rôle,
    *               et le jeton CSRF extrait du JWT.
    *
    * @throws Exception Si le JWT n'est pas trouvé dans le cookie, s'il est expiré, si des champs requis sont manquants
    *                   dans le JWT décodé, ou si une autre erreur se produit lors du décodage.
  */
  public function decodeJWTFromCookie() {
      $secretKey = $_ENV['SECRET_KEY'];

      if(!isset($_COOKIE['token'])) {
          throw new Exception("JWT non trouvé dans le cookie");
      }

      try{
          $jwt = $_COOKIE['token'];
          $decoded = JWT::decode($jwt, new Key($secretKey, "HS256"));

          if(!isset($decoded->id) || !isset($decoded->userName) || !isset($decoded->roleName) || !isset($decoded->csrfToken)) {
              error_log("JWT décodé, manque de champ requis");
              throw new Exception("Données JWT incomplètes");
          }

          return [
              'id' => $decoded->id,
              'userName' => $decoded->userName,
              'roleName' => $decoded->roleName,
              'csrfToken' => $decoded->csrfTokene
          ];

      } catch (ExpiredException $e) {
          throw new Exception("JWT expiré");
      } catch (Exception $e) {
          throw new Exception("Erreur lors du décodage du JWT: " . $e->getMessage());
      }
  }


}
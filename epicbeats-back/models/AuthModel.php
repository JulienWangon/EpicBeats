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


}
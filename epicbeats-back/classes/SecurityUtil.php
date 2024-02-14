<?php

class SecurityUtil {

    /**
     * Génère un jeton sécurisé aléatoire.
     *
     * Cette méthode utilise la fonction `random_bytes` pour générer une séquence de bytes aléatoires
     * de longueur 32, puis convertit cette séquence en une chaîne hexadécimale à l'aide de `bin2hex`.
     * Le jeton généré est hautement sécurisé et convient bien pour des cas d'utilisation tels que les
     * jetons d'authentification, les jetons CSRF, ou tout autre scénario nécessitant un niveau élevé
     * de sécurité et d'aléatoire.
     *
     * @return string Le jeton sécurisé généré, sous forme de chaîne hexadécimale.
    */
    public static function generateSecureToken() {
        return bin2hex(random_bytes(32));
    } 


    /**
      * Génère un mot de passe temporaire aléatoire.
      *
      * Cette méthode crée un mot de passe temporaire sécurisé en s'assurant qu'il contient au moins une lettre majuscule,
      * une lettre minuscule, un chiffre et un caractère spécial. Le reste du mot de passe est complété avec un mélange
      * aléatoire de ces types de caractères jusqu'à atteindre la longueur désirée, qui est définie par la variable $length.
      * Ensuite, le mot de passe est mélangé pour disperser les types de caractères de manière aléatoire. Cette approche
      * vise à renforcer la sécurité en évitant des mots de passe prévisibles.
      *
      * @return string Le mot de passe temporaire généré.
    */
    public static function generateTemporaryPassword() {
        $length = 12;
        $uppercase = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $lowercase = 'abcdefghijklmnopqrstuvwxyz';
        $digits = '0123456789';
        $specialChars = '!@#$%^&*()_+[]{}|;:,.<>?';

        $password = '';

        // Au moins une majuscule
        $password .= $uppercase[rand(0, strlen($uppercase) - 1)];
        // Au moins une minuscule
        $password .= $lowercase[rand(0, strlen($lowercase) - 1)];
        // Au moins un chiffre
        $password .= $digits[rand(0, strlen($digits) - 1)];    
        // Au moins un caractère spécial
        $password .= $specialChars[rand(0, strlen($specialChars) - 1)];    
        // Remplir le reste du mot de passe avec des caractères aléatoires
        $remainingLength = $length - strlen($password);
        $allChars = $uppercase . $lowercase . $digits . $specialChars;
        for ($i = 0; $i < $remainingLength; $i++) {
            $password .= $allChars[rand(0, strlen($allChars) - 1)];
        }    
        // Mélanger les caractères du mot de passe
        $password = str_shuffle($password);

        return $password;
    }







}
<?php

class SecurityUtil {

  public static function generateSecureToken() {
      return bin2hex(random_bytes(32));
  } 





}
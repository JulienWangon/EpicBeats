<?php

//configuration CORS
//localhost:3000 access (accès autorisé à l'API)
header("Access-Control-Allow-Origin: http://localhost:3000");
//allowed HTTP methods (méthode autorisées)
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
//Allowed headers (en-tête autorisés)
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-CSRF-TOKEN");
header("Access-Control-Allow-Credentials: true");


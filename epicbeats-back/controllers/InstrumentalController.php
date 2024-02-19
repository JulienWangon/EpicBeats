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




}
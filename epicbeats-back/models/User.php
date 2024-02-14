<?php

class Users {

    private ?int $id;
    private string $userName;
    private string $email;
    private string $password;
    private int $idRole;


    public function __construct(string $userName = null, string $email = null, string $password = null, int $idRole = null, ?int $id = null) {

        $this->userName = $userName;
        $this->email = $email;
        $this->password = $password;
        $this->idRole = $idRole;
        $this->id = $id;
    }

    //GETTER
    public function getId() :int {
        return $this->id;
    }

    public function getUserName() :string {
        return $this->userName;
    }

    public function getEmail() :string {
        return $this->email;
    }

    public function getPassword() :string {
        return $this->password;
    }

    public function getIdRole() :int {
        return $this->idRole;
    }

    
    //SETTER
    public function setId($id) :void {
        $this->id = $id;
    }

    public function setUserName($userName) :void {
        $this->userName = $userName;
    }

    public function setEmail($email) :void {  
      $this->email = $email;
    }

    public function setPassword($password) :void {
        $this->password = $password;
    }

    public function setIdRole($idRole) :void {
        $this->idRole = $idRole;
    }
}
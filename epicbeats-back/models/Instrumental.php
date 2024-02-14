<?php

class Instrumental {

    private ?int $id;
    private string $title;
    private string $gender;
    private int $bpm;
    private string $coverPath;
    private string $soundPath;
    private float $price;


    public function __construct(string $title, string $gender, int $bpm, string $coverPath, string $soundPath, float $price, ?int $id = null) {

        $this->title = $title;
        $this->gender = $gender;
        $this->bpm = $bpm;
        $this->coverPath = $coverPath;
        $this->soundPath = $soundPath;
        $this->price = $price;
        $this->id = $id;
    }

    public function getId() :?int {
      return $this->id;
    }

    public function getTitle() :string {
        return $this->title;
    }

    public function getGender() :string {
      return $this->gender;
    }

    public function getBpm() :int {
        return $this->bpm;
    }

    public function getCoverPath() :string {
        return $this->coverPath;
    }

    public function getsoundPath() :string {
        return $this->soundPath;
    }

    public function getPrice() :float {
        return$this->price;
    }



    public function setId($id) :void {
        $this->id = $id;
    }

    public function setTitle($title) :void {
        $this->title = $title;
    }

    public function setGender($gender) :void {
        $this->gender = $gender;
    }

    public function setBpm($bpm) :void {
        $this->bpm = $bpm;
    }

    public function setCoverPath($coverPath) :void {
        $this->coverPath = $coverPath;
    }

    public function setSoundPath($soundPath) :void {
        $this->soundPath = $soundPath;
    }

    public function setPrice($price) :void {
        $this->price = $price;
    }

}
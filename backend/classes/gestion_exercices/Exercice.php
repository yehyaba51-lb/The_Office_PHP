<?php
    require_once('ExerciceManager.php');

    class Exercice{
        private $exercice_id;
        private $lecon_id;
        private $cours_id;
        private $exercice_titre;
        private $manager;

        public function __construct(){
            $this->manager = new ExerciceManager();
        }


        public function getExerciceId(){
            return $this->exercice_id;
        }

        public function setExerciceId($exercice_id){
            $this->exercice_id = $exercice_id;
        }

        public function getLeconId(){
            return $this->lecon_id;
        }

        public function setLeconId($lecon_id){
            $this->lecon_id = $lecon_id;
        }

        public function getCoursId(){
            return $this->cours_id;
        }

        public function setCoursId($cours_id){
            $this->cours_id = $cours_id;
        }

        public function getTitre(){
            return $this->exercice_titre;
        }

        public function setTitre($exercice_titre){
            $this->exercice_titre = $exercice_titre;
        }

        

    }
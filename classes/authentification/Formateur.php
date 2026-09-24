<?php
    require_once('Utilisateur.php');
    require_once('ExerciceManager.php');

    class Formateur extends Utilisateur {
        private $manager;

        public function __construct(){
            $this->manager = new ExerciceManager();
        }

        public function corrigerExercice($id, $data){
            return $this->manager->corrigerSoumission($id, $data);
        }
    }
<?php
    require_once('Utilisateur.php');
    require_once('UtilisateurManager.php');


    class Administrateur extends Utilisateur {
        private $manager;

        public function __construct(){
            $this->manager = new UtilisateurManager();
        }

        public function creerCompte($data){
            return $this->manager->creerUtilisateur($data);
        }
    }
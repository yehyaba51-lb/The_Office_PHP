<?php
    require_once('Utilisateur.php');
    require_once(__DIR__ . '/../gestion_exercices/ExerciceManager.php');


    // Placeholder for future role-specific logic (admin-only rules, ownership checks).
    // Currently empty — Administrateur.php is not
    // instantiated anywhere. Role checks live in Authentification.
    class Administrateur extends Utilisateur {
        private $manager;

        public function __construct(){
            $this->manager = new UtilisateurManager();
        }

        public function creerCompte($data){
            return $this->manager->creerUtilisateur($data);
        }
    }
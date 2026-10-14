<?php
    require_once('Utilisateur.php');
    require_once(__DIR__ . '/../gestion_exercices/ExerciceManager.php');

    // Placeholder for future role-specific logic (formateur ownership, cours access rules).
    // Currently empty — Formateur.php is not
    // instantiated anywhere. Role checks live in Authentification.
    class Formateur extends Utilisateur {
        private $manager;

        public function __construct(){
            $this->manager = new ExerciceManager();
        }

        public function corrigerExercice($soumission_id, $formateur_id, $note, $commentaire){
            return $this->manager->corrigerSoumission($soumission_id, $formateur_id, $note, $commentaire);
        }
    }
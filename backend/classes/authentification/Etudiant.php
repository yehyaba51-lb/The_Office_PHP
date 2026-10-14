<?php
    require_once('Utilisateur.php');
    require_once(__DIR__ . '/../gestion_exercices/ExerciceManager.php');

    // Placeholder for future role-specific logic (enrolment checks, progression access).
    // Currently empty — Etudiant.php is not
    // instantiated anywhere. Role checks live in Authentification.
    class Etudiant extends Utilisateur {
        private $manager;

        public function __construct(){
            $this->manager = new ExerciceManager();
        }

        public function soumettreReponseTexte($etudiant_id, $question_id, $soumission_reponse){
            return $this->manager->creerSoumissionText($etudiant_id, $question_id, $soumission_reponse);
        }
    }
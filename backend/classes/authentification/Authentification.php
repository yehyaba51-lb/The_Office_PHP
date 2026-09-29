<?php
    require_once(__DIR__ . '/../acces_donnees/BaseDeDonnee.php');
    require_once(__DIR__ . '/../acces_donnees/UtilisateurModel.php');
    require_once(__DIR__ . '/../acces_donnees/CoursModel.php');
    require_once(__DIR__ . '/../acces_donnees/InscriptionModel.php');
    
    class Authentification{
        private $model;
        private $inscriptionModel;
        private $coursModel;

        public function __construct(){
            $db = new BaseDeDonnee();
            $this->model = new UtilisateurModel($db);
            $this->inscriptionModel = new InscriptionModel($db);
            $this->coursModel = new CoursModel($db);
        }


        public function connecter($email, $mot_de_passe){
            $row = $this->model->getUtilisateurByEmail($email);

            if(!$row){
                return false;
            }
            if(!password_verify($mot_de_passe, $row['mot_de_passe'])){
                return false;
            }

            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }

            session_regenerate_id(true);
            $_SESSION['utilisateur_id'] = $row['utilisateur_id'];
            $_SESSION['role'] = $row['role'];
            
            return $row;
        }
        
        public function deconnecter(){
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }

            if(!isset($_SESSION['utilisateur_id'])){
                return false;
            }

            session_destroy();

            return true;
        }

        public function verifierSession(){
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }

            if(!isset($_SESSION['utilisateur_id'])){
                return false;
            }

            $user = $this->model->getUtilisateur($_SESSION['utilisateur_id']);

            if(!$user){
                return false;
            }

            return $user;
        }

        public function verifierRole($role){
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }
            
            if(!isset($_SESSION['role'])){
                return false;
            }

            return $_SESSION['role'] === $role;
        }

        public function verifierAccesEtudiant($etudiant_id, $cours_id){
           $inscrit = $this->inscriptionModel->dejaInscrit($etudiant_id, $cours_id);
           
           if($inscrit === false){
            return false;
           }

           return true;
        }

        public function verifierAccesFormateur($formateur_id, $cours_id){
            $accesGranted = $this->coursModel->verifierAccesFormateur($formateur_id, $cours_id);

            if($accesGranted === false){
            return false;
           }

           return true;
        }
    }
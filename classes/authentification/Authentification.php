<?php
    require_once(__DIR__ . '/../acces_donnees/BaseDeDonnee.php');
    require_once(__DIR__ . '/../acces_donnees/UtilisateurModel.php');
    
    class Authentification{
        private $model;

        public function __construct(){
            $db = new BaseDeDonnee();
            $this->model = new UtilisateurModel($db);
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
    }
<?php
    require_once(__DIR__ . '/../acces_donnees/BaseDeDonnee.php');
    require_once(__DIR__ . '/../acces_donnees/UtilisateurModel.php');
    require_once('Utilisateur.php');

    class UtilisateurManager{
        private $model;

        public function __construct(){
            $db = new BaseDeDonnee();
            $this->model = new UtilisateurModel($db);
        }

        public function getUtilisateur($id){
            $row =  $this->model->getUtilisateur($id);

            if(!$row){
                return false;
            }

            $utilisateur = new Utilisateur();
            $utilisateur->setUtilisateurId($row['utilisateur_id']);
            $utilisateur->setPrenom($row['prenom']);
            $utilisateur->setNom($row['nom']);
            $utilisateur->setEmail($row['email']);
            $utilisateur->setRole($row['role']);
            $utilisateur->setCreeLe($row['cree_le']);

            return $utilisateur;
        }

        public function getAllUtilisateurs(){
            $rows = $this->model->getAllUtilisateurs();
            $utilisateurs = [];

            foreach ($rows as $row) {
                $utilisateur = new Utilisateur();
                $utilisateur->setUtilisateurId($row['utilisateur_id']);
                $utilisateur->setPrenom($row['prenom']);
                $utilisateur->setNom($row['nom']);
                $utilisateur->setEmail($row['email']);
                $utilisateur->setRole($row['role']);
                $utilisateur->setCreeLe($row['cree_le']);

                $utilisateurs[] = $utilisateur;

                }
            
            return $utilisateurs;
        }

        public function getFormateurs(){
            return $this->model->getFormateurs();
        }

        public function creerUtilisateur($data){
            return $this->model->creerUtilisateur($data);
        }

        public function updateUtilisateur($id, $data){
            return $this->model->updateUtilisateur($id, $data);
        }

        public function reinitialiserMotDePasse($id){
            return $this->model->reinitialiserMotDePasse($id);
        }

        public function supprimerUtilisateur($id){
            return $this->model->supprimerUtilisateur($id);
        }
        
    }

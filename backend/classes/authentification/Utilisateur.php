<?php
    class Utilisateur {
        private $utilisateur_id;
        private $prenom;
        private $nom;
        private $email;
        private $mot_de_passe;
        private $role;
        private $cree_le;


        public function setUtilisateurId($utilisateur_id) {
            $this->utilisateur_id = $utilisateur_id;
        }

        public function getUtilisateurId() {
            return $this->utilisateur_id;
        }

        public function getPrenom() {
            return $this->prenom;
        }
        public function setPrenom($prenom) {
            $this->prenom = $prenom;
        }

        public function getNom() {
            return $this->nom;
        }
        public function setNom($nom) {
            $this->nom = $nom;
        }

        public function getEmail() {
            return $this->email;
        }
        public function setEmail($email) {
            $this->email = $email;
        }

        public function getRole() {
            return $this->role;
        }
        public function setRole($role) {
            $this->role = $role;
        }

        public function setCreeLe($cree_le) {
            $this->cree_le = $cree_le;
        }

        public function getCreeLe() {
            return $this->cree_le;
        }

        public function setMotDePasse($mot_de_passe) {
            $this->mot_de_passe = $mot_de_passe;
        }

        public function getNomComplet() {
            return $this->prenom . ' ' . $this->nom;
        }

    }
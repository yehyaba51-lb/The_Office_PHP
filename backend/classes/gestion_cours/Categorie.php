<?php
    class Categorie {
        private $categorie_id;
        private $categorie_nom;

        public function getCategorieId(){
            return $this->categorie_id;
        }

        public function setCategorieId($categorie_id){
            $this->categorie_id = $categorie_id;
        }

        public function getCategorieNom(){
            return $this->categorie_nom;
        }

        public function setCategorieNom($categorie_nom){
            $this->categorie_nom = $categorie_nom;
        }


    }
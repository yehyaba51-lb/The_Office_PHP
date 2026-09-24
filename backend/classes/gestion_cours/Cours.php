<?php
    class Cours {
        private $cours_id;
        private $cours_titre;
        private $description;
        private $formateur_id;
        private $categorie_id;
        private $cree_le;
        private $url_image;

        public function getCoursId(){
            return $this->cours_id;
        }

        public function setCoursId($cours_id){
            $this->cours_id = $cours_id;
        }

        public function getTitre(){
            return $this->cours_titre;
        }

        public function setTitre($cours_titre){
            $this->cours_titre = $cours_titre;
        }

        public function getDescription(){
            return $this->description;
        }

        public function setDescription($description){
            $this->description = $description;
        }

        public function getFormateurId(){
            return $this->formateur_id;
        }

        public function setFormateurId($formateur_id){
            $this->formateur_id = $formateur_id;
        }

        public function getCategorieId(){
            return $this->categorie_id;
        }

        public function setCategorieId($categorie_id){
            $this->categorie_id = $categorie_id;
        }

        public function getCreeLe(){
            return $this->cree_le;
        }

        public function setCreeLe($cree_le){
            $this->cree_le = $cree_le;
        }

        public function getUrlImage(){
            return $this->url_image;
        }

        public function setUrlImage($url_image){
            $this->url_image = $url_image;
        }

    }
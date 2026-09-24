<?php
    class Lecon {
        private $lecon_id;
        private $cours_id;
        private $lecon_titre;
        private $ordre;


        public function getLeconId(){
            return $this->lecon_id;
        }

        public function setLeconId($lecon_id){
            $this->lecon_id = $lecon_id;
        }

        public function getCoursId(){
            return $this->cours_id;
        }

        public function setCoursId($cours_id){
            $this->cours_id = $cours_id;
        }
        public function getTitre(){
            return $this->lecon_titre;
        }

        public function setTitre($lecon_titre){
            $this->lecon_titre = $lecon_titre;
        }

        public function getOrdre(){
            return $this->ordre;
        }

        public function setOrdre($ordre){
            $this->ordre = $ordre;
        }
    }
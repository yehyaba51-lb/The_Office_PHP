<?php
    abstract class ContenuLecon {
        protected $lecon_id;
        protected $cours_id;
        protected $ordre;

        abstract public function afficherContenu();

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

        public function getOrdre(){
            return $this->ordre;
        }

        public function setOrdre($ordre){
            $this->ordre = $ordre;
        }
    }
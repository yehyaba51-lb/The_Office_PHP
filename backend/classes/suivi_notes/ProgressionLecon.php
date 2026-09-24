<?php
    class ProgressionLecon{
        private $progression_lecon_id;
        private $etudiant_id;
        private $cours_id;
        private $lecon_id;
        private $complete_le;
        private $statut;

        public function getProgressionLeconId(){
            return $this->progression_lecon_id;
        }

        public function setProgressionLeconId($progression_lecon_id){
            $this->progression_lecon_id = $progression_lecon_id;
        }

        public function getEtudiantId(){
            return $this->etudiant_id;
        }

        public function setEtudiantId($etudiant_id){
            $this->etudiant_id = $etudiant_id;
        }

        public function getCoursId(){
            return $this->cours_id;
        }

        public function setCoursId($cours_id){
            $this->cours_id = $cours_id;
        }

        public function getLeconId(){
            return $this->lecon_id;
        }

        public function setLeconId($lecon_id){
            $this->lecon_id = $lecon_id;
        }

        public function getCompleteLe(){
            return $this->complete_le;
        }

        public function setCompleteLe($complete_le){
            $this->complete_le = $complete_le;
        }

        public function getStatut(){
            return $this->statut;
        }

        public function setStatut($statut){
            $this->statut = $statut;
        }
    }


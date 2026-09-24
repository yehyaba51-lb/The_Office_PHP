<?php
    class Progression{
        private $progression_id;
        private $etudiant_id;
        private $cours_id;
        private $complete_le;
        private $derniere_lecon_id;
        private $modifie_le;

        public function getProgressionId(){
            return $this->progression_id;
        }

        public function setProgressionId($progression_id){
            $this->progression_id = $progression_id;
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

        public function getCompleteLe(){
            return $this->complete_le;
        }

        public function setCompleteLe($complete_le){
            $this->complete_le = $complete_le;
        }

        public function getDerniereLeconId(){
            return $this->derniere_lecon_id;
        }

        public function setDerniereLeconId($derniere_lecon_id){
            $this->derniere_lecon_id = $derniere_lecon_id;
        }

        public function estComplete(){
            return $this->complete_le !== null;
        }

        public function getModifieLe(){
            return $this->modifie_le;
        }

        public function setModifieLe($modifie_le){
            $this->modifie_le = $modifie_le;
        }

    }
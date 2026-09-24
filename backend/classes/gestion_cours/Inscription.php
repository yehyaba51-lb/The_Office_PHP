<?php
    class Inscription {
        private $inscription_id;
        private $etudiant_id;
        private $cours_id;
        private $inscrit_le;
        private $note_finale;

        public function getInscriptionId(){
            return $this->inscription_id;
        }

        public function setInscriptionId($inscription_id){
            $this->inscription_id = $inscription_id;

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

        public function getInscritLe(){
            return $this->inscrit_le;
        }

        public function setInscritLe($inscrit_le){
            $this->inscrit_le = $inscrit_le;
        }

        public function getNoteFinale(){
            return $this->note_finale;
        }

        public function setNoteFinale($note_finale){
            $this->note_finale = $note_finale;
        }

    }
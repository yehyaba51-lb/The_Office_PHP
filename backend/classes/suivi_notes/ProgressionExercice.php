<?php
    class ProgressionExercice{
        private $progression_exercice_id;
        private $etudiant_id;
        private $exercice_id;
        private $complete_le;
        private $note;
        private $statut;

        public function getProgressionExerciceId(){
            return $this->progression_exercice_id;
        }

        public function setProgressionExerciceId($progression_exercice_id){
            $this->progression_exercice_id = $progression_exercice_id;
        }

        public function getEtudiantId(){
            return $this->etudiant_id;
        }

        public function setEtudiantId($etudiant_id){
            $this->etudiant_id = $etudiant_id;
        }

        public function getExerciceId(){
            return $this->exercice_id;
        }

        public function setExerciceId($exercice_id){
            $this->exercice_id = $exercice_id;
        }

        public function getNote(){
            return $this->note;
        }

        public function setNote($note){
            $this->note = $note;
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
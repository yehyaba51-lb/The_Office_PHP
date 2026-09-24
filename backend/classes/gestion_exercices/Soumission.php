<?php
    class Soumission{
        private $soumission_id;
        private $etudiant_id;
        private $question_id;
        private $soumission_reponse;
        private $url_fichier;
        private $soumis_le;
        private $corrige_le;
        private $corrige_par;
        private $note;
        private $commentaire;

        public function getSoumissionId(){
            return $this->soumission_id;
        }

        public function setSoumissionId($soumission_id){
            $this->soumission_id = $soumission_id;
        }

        public function getEtudiantId(){
            return $this->etudiant_id;
        }

        public function setEtudiantId($etudiant_id){
            $this->etudiant_id = $etudiant_id;
        }

        public function getQuestionId(){
            return $this->question_id;
        }

        public function setQuestionId($question_id){
            $this->question_id = $question_id;
        }

        public function getSoumissionReponse(){
            return $this->soumission_reponse;
        }
        public function setSoumissionReponse($soumission_reponse){
            $this->soumission_reponse = $soumission_reponse;
        }
        public function getUrlFichier(){
            return $this->url_fichier;
        }

        public function setUrlFichier($url_fichier){
            $this->url_fichier = $url_fichier;
        }

        public function getSoumisLe(){
            return $this->soumis_le;
        }

        public function setSoumisLe($soumis_le){
            $this->soumis_le = $soumis_le;
        }

        public function getNote(){
            return $this->note;
        }

        public function setNote($note){
            $this->note = $note;
        }

        public function getCorrigeLe(){
            return $this->corrige_le;
        }

        public function setCorrigeLe($corrige_le) {
            $this->corrige_le = $corrige_le;
        }

        public function getCorrigePar(){
            return $this->corrige_par;
        } 

        public function setCorrigePar($corrige_par){
            $this->corrige_par = $corrige_par;
        }

        public function getCommentaire(){
            return $this->commentaire;
        }

        public function setCommentaire($commentaire){
            $this->commentaire = $commentaire;
        }

    }
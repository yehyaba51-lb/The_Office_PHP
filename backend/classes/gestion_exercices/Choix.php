<?php
    class Choix{
        private $choix_id;
        private $question_id;
        private $texte_choix;
        private $est_correct;

        public function getChoixId(){
            return $this->choix_id;
        }

        public function setChoixId($choix_id){
            $this->choix_id = $choix_id;
        }

        public function getQuestionId(){
            return $this->question_id;
        }

        public function setQuestionId($question_id){
            $this->question_id = $question_id;
        }

        public function getTexteChoix(){
            return $this->texte_choix;
        }

        public function setTexteChoix($texte_choix){
            $this->texte_choix = $texte_choix;
        }

        public function getEstCorrect(){
            return $this->est_correct;
        }

        public function setEstCorrect($est_correct){
            $this->est_correct = $est_correct;
        }  
    }
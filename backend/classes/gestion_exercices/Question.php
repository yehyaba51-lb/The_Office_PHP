<?php
    class Question{
        private $question_id;
        private $exercice_id;
        private $texte_question;
        private $question_type;

        public function getQuestionId(){
            return $this->question_id;
        }

        public function setQuestionId($question_id){
            $this->question_id = $question_id;
        }

        public function getExerciceId(){
            return $this->exercice_id;
        }

        public function setExerciceId($exercice_id){
            $this->exercice_id = $exercice_id;
        }

        public function getTexteQuestion(){
            return $this->texte_question;
        }

        public function setTexteQuestion($texte_question){
            $this->texte_question = $texte_question;
        }

        public function getQuestionType(){
            return $this->question_type;
        }

        public function setQuestionType($question_type){
            $this->question_type = $question_type;
        }

    }
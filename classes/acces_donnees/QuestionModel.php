<?php
    require_once('BaseDeDonnee.php');

    class QuestionModel{
        private $conn;

        public function __construct(BaseDeDonnee $db){
            $this->conn = $db->getConn();
        }

        // question table
        public function getQuestion($id){
            $stmt = mysqli_prepare($this->conn, "SELECT * FROM question WHERE question_id = ?");

            if (!$stmt) {
                error_log('Prepare failed: ' . mysqli_error($this->conn));
                return false;
            }

            mysqli_stmt_bind_param($stmt, "i", $id);
            $execute = mysqli_stmt_execute($stmt);

            if($execute === false){
                return false;
            }

            $result = mysqli_stmt_get_result($stmt);

            return mysqli_fetch_assoc($result);
        }

        public function getAllQuestions(){
            $query = "SELECT * FROM question";

            $result = mysqli_query($this->conn, $query);

            if (!$result) {
                error_log('Query failed: ' . mysqli_error($this->conn));
                return false;
            }

            return mysqli_fetch_all($result, MYSQLI_ASSOC);
        }

        public function getQuestionsByExercice($exercice_id){
            $stmt = mysqli_prepare($this->conn, 
                "SELECT *
                FROM question
                WHERE exercice_id = ?"
            );

            if(!$stmt){
                error_log('Prepare failed: ' . mysqli_error($this->conn));
                return false;
            }

            mysqli_stmt_bind_param($stmt, "i", $exercice_id);
            $execute = mysqli_stmt_execute($stmt);

            if($execute === false){
                return false;
            }

            $result = mysqli_stmt_get_result($stmt);

            return mysqli_fetch_all($result, MYSQLI_ASSOC);
        }

        public function creerQuestion($data){
            $stmt = mysqli_prepare($this->conn, "INSERT INTO question(exercice_id, texte_question, question_type) VALUES(?, ?, ?)");

            if (!$stmt) {
                error_log('Prepare failed: ' . mysqli_error($this->conn));
                return false;
            }

            mysqli_stmt_bind_param($stmt, "iss", $data['exercice_id'], $data['texte_question'], $data['question_type']);
            $execute = mysqli_stmt_execute($stmt);
            if($execute === false){
                return false;
            }

            return mysqli_insert_id($this->conn);
        }

        public function updateQuestion($id, $data){
            $stmt = mysqli_prepare($this->conn, "UPDATE question SET texte_question = ?, question_type = ? WHERE question_id = ?");

            if (!$stmt) {
                error_log('Prepare failed: ' . mysqli_error($this->conn));
                return false;
            }

            mysqli_stmt_bind_param($stmt, "ssi", $data['texte_question'], $data['question_type'], $id);
            $execute = mysqli_stmt_execute($stmt);
            if($execute === false){
                return false;
            }

            return $execute;
        }

        public function supprimerQuestion($id){
            $stmt = mysqli_prepare($this->conn, "DELETE FROM question WHERE question_id = ?");

            if (!$stmt) {
                error_log('Prepare failed: ' . mysqli_error($this->conn));
                return false;
            }

            mysqli_stmt_bind_param($stmt, "i", $id);
            $execute = mysqli_stmt_execute($stmt);
            if($execute === false){
                return false;
            }

            return $execute;
        }
    }
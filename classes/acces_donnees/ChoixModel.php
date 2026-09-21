<?php
    require_once('BaseDeDonnee.php');

    class ChoixModel{
        private $conn;

        public function __construct(BaseDeDonnee $db){
            $this->conn = $db->getConn();
        }


        // choix table
        public function getChoix($id){
            $stmt = mysqli_prepare($this->conn, "SELECT * FROM choix WHERE choix_id = ?");

            if (!$stmt) {
                error_log('Prepare failed: ' . mysqli_error($this->conn));
                return false;
            }

            mysqli_stmt_bind_param($stmt, "i", $id);
            mysqli_stmt_execute($stmt);

            $result = mysqli_stmt_get_result($stmt);

            return mysqli_fetch_assoc($result);
        }

        public function getAllChoix(){
            $query = "SELECT * FROM choix";

            $result = mysqli_query($this->conn, $query);

            if (!$result) {
                error_log('Query failed: ' . mysqli_error($this->conn));
                return false;
            }

            return mysqli_fetch_all($result, MYSQLI_ASSOC);
        }

        public function getChoixByQuestion($question_id){
            $stmt = mysqli_prepare($this->conn, 
                "SELECT *
                FROM choix
                WHERE question_id = ?"
            );

            if(!$stmt){
                error_log('Prepare failed: ' . mysqli_error($this->conn));
                return false;
            }

            mysqli_stmt_bind_param($stmt, "i", $question_id);
            mysqli_stmt_execute($stmt);

            $result = mysqli_stmt_get_result($stmt);

            return mysqli_fetch_all($result, MYSQLI_ASSOC);
        }
        public function getChoixByExercice($exercice_id){
            $stmt = mysqli_prepare($this->conn, 
                "SELECT DISTINCT ch.*
                FROM choix AS ch
                INNER JOIN question AS q ON q.question_id = ch.question_id
                WHERE q.exercice_id = ?
                ORDER BY ch.question_id, ch.ordre"
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

        public function creerChoix($data){
            $stmt = mysqli_prepare($this->conn, "INSERT INTO choix(question_id, texte_choix, est_correct) VALUES(?, ?, ?)");

            if (!$stmt) {
                error_log('Prepare failed: ' . mysqli_error($this->conn));
                return false;
            }

            mysqli_stmt_bind_param($stmt, "isi", $data['question_id'], $data['texte_choix'], $data['est_correct']);
            mysqli_stmt_execute($stmt);

            return mysqli_insert_id($this->conn);
        }

        public function updateChoix($id, $data){
            $stmt = mysqli_prepare($this->conn, "UPDATE choix SET texte_choix = ?, est_correct = ? WHERE choix_id = ?");

            if (!$stmt) {
                error_log('Prepare failed: ' . mysqli_error($this->conn));
                return false;
            }

            mysqli_stmt_bind_param($stmt, "sii", $data['texte_choix'], $data['est_correct'], $id);
            return mysqli_stmt_execute($stmt);
        }

        public function supprimerChoix($id){
            $stmt = mysqli_prepare($this->conn, "DELETE FROM choix WHERE choix_id = ?");

            if (!$stmt) {
                error_log('Prepare failed: ' . mysqli_error($this->conn));
                return false;
            }

            mysqli_stmt_bind_param($stmt, "i", $id);
            return mysqli_stmt_execute($stmt);
        }
    }
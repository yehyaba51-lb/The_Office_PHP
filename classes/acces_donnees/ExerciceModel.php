<?php
    require_once('BaseDeDonnee.php');

    class ExerciceModel{
        private $conn;

        public function __construct(BaseDeDonnee $db){
            $this->conn = $db->getConn();
        }

        // exercice table
        public function getExercice($id){
            $stmt = mysqli_prepare($this->conn, "SELECT * FROM exercice WHERE exercice_id = ?");

            if (!$stmt) {
                error_log('Prepare failed: ' . mysqli_error($this->conn));
                return false;
            }

            mysqli_stmt_bind_param($stmt, "i", $id);
            mysqli_stmt_execute($stmt);

            $result = mysqli_stmt_get_result($stmt);

            return mysqli_fetch_assoc($result);
        }

        public function getAllExercices(){
            $query = "SELECT * FROM exercice";

            $result = mysqli_query($this->conn, $query);

            if (!$result) {
                error_log('Query failed: ' . mysqli_error($this->conn));
                return false;
            }

            return mysqli_fetch_all($result, MYSQLI_ASSOC);
        }

        public function getExercicesByLecon($lecon_id, $cours_id){
            $stmt = mysqli_prepare($this->conn, 
            "SELECT *
            FROM exercice
            WHERE lecon_id = ?
            AND cours_id = ?");

            if(!$stmt){
                error_log('Prepare failed: ' . mysqli_error($this->conn));
                return false;
            }
            mysqli_stmt_bind_param($stmt, "ii", $lecon_id, $cours_id);
            mysqli_stmt_execute($stmt);



            $result = mysqli_stmt_get_result($stmt);
            return mysqli_fetch_all($result, MYSQLI_ASSOC);
        }

        public function creerExercice($data){
            if(empty($data['exercice_titre']) || strlen(trim($data['exercice_titre'])) < 2 ){
                return false;
            }

            $stmt = mysqli_prepare($this->conn, "INSERT INTO exercice(cours_id, lecon_id, exercice_titre) VALUES(?, ?, ?)");

            if (!$stmt) {
                error_log('Prepare failed: ' . mysqli_error($this->conn));
                return false;
            }

            mysqli_stmt_bind_param($stmt, "iis", $data['coursId'], $data['leconId'], $data['exercice_titre']);
            mysqli_stmt_execute($stmt);

            return mysqli_insert_id($this->conn);
        }


        public function supprimerExercice($id){
            $stmt = mysqli_prepare($this->conn, "DELETE FROM exercice WHERE exercice_id = ?");

            if (!$stmt) {
                error_log('Prepare failed: ' . mysqli_error($this->conn));
                return false;
            }

            mysqli_stmt_bind_param($stmt, "i", $id);
            return mysqli_stmt_execute($stmt);
        }
    }
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
            $execute = mysqli_stmt_execute($stmt);

            if($execute === false){
                return false;
            }

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

        public function getExercicesByCours($cours_id){
            $stmt = mysqli_prepare($this->conn, 
                "SELECT *
                FROM exercice
                WHERE cours_id = ?"
            );

            if(!$stmt){
                error_log('Prepare failed: ' . mysqli_error($this->conn));
                return false;
            }
            mysqli_stmt_bind_param($stmt, "i", $cours_id);
            $execute = mysqli_stmt_execute($stmt);

            if($execute === false){
                return false;
            }

            $result = mysqli_stmt_get_result($stmt);
            return mysqli_fetch_all($result, MYSQLI_ASSOC);
        }

        public function getExercicesByLecon($lecon_id, $cours_id){
            $stmt = mysqli_prepare($this->conn, 
                "SELECT *
                FROM exercice
                WHERE lecon_id = ?
                AND cours_id = ?"
            );

            if(!$stmt){
                error_log('Prepare failed: ' . mysqli_error($this->conn));
                return false;
            }
            mysqli_stmt_bind_param($stmt, "ii", $lecon_id, $cours_id);
            $execute = mysqli_stmt_execute($stmt);

            if($execute === false){
                return false;
            }

            $result = mysqli_stmt_get_result($stmt);
            return mysqli_fetch_all($result, MYSQLI_ASSOC);
        }

        public function creerExercice($data){
            if(empty($data['exercice_titre']) || strlen(trim($data['exercice_titre'])) < 2 || !preg_match("/^[a-zA-ZÀ-ÿ0-9' :\-,.!?;()\n]*$/u", $data['exercice_titre'])){
                return false;
            }

            $stmt = mysqli_prepare($this->conn, "INSERT INTO exercice(cours_id, lecon_id, exercice_titre) VALUES(?, ?, ?)");

            if (!$stmt) {
                error_log('Prepare failed: ' . mysqli_error($this->conn));
                return false;
            }

            $exercice_titre = strtolower($data['exercice_titre']);
            mysqli_stmt_bind_param($stmt, "iis", $data['cours_id'], $data['lecon_id'], $exercice_titre);
            $success = mysqli_stmt_execute($stmt);
            if(!$success){
                return false;
            }
            return mysqli_insert_id($this->conn);
        }


        public function supprimerExercice($id){
            $stmt = mysqli_prepare($this->conn, "DELETE FROM exercice WHERE exercice_id = ?");

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
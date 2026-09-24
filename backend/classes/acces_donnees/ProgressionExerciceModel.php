<?php
    require_once('BaseDeDonnee.php');

    class ProgressionExerciceModel{
        private $conn;

        public function __construct(BaseDeDonnee $db){
            $this->conn = $db->getConn();
        }

        public function getProgressionExercice($id){
            $stmt = mysqli_prepare($this->conn, 
                "SELECT *
                FROM progression_exercice
                WHERE progression_exercice_id = ?"
            );

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

        public function getAllProgressionExercice(){
            $query = "SELECT * FROM progression_exercice";

            $result = mysqli_query($this->conn, $query);

            if (!$result) {
                error_log('Query failed: ' . mysqli_error($this->conn));
                return false;
            }

            return mysqli_fetch_all($result, MYSQLI_ASSOC);
        }

        public function getProgressionByExerciceEtudiant($etudiant_id, $exercice_id){
            $stmt = mysqli_prepare($this->conn, 
            "SELECT *
            FROM progression_exercice
            WHERE etudiant_id = ?
            AND exercice_id = ?");

            if(!$stmt){
                error_log('Prepare failed' . mysqli_error($this->conn));
                return false;
            }

            mysqli_stmt_bind_param($stmt, "ii", $etudiant_id, $exercice_id);
            $execute = mysqli_stmt_execute($stmt);

            if($execute === false){
                return false;
            }

            $result = mysqli_stmt_get_result($stmt);

            return mysqli_fetch_assoc($result);
        }

        public function creerProgressionExercice($data){
            $stmt = mysqli_prepare($this->conn, 
                "INSERT INTO progression_exercice(etudiant_id, exercice_id, complete_le, statut, note)
                VALUES(?, ?, ?, ?, ?)"
            );

            if (!$stmt) {
                error_log('Prepare failed: ' . mysqli_error($this->conn));
                return false;
            }

            mysqli_stmt_bind_param($stmt, "iissd", $data['etudiant_id'], $data['exercice_id'], $data['complete_le'], $data['statut'], $data['note']);
            $execute = mysqli_stmt_execute($stmt);

            if($execute === false){
                return false;
            }

            return mysqli_insert_id($this->conn);
        }


        public function updateProgressionExercice($data){
            if(isset($data['note'])){
                $stmt = mysqli_prepare($this->conn, 
                    "UPDATE progression_exercice
                    SET note = ?
                    WHERE etudiant_id = ?
                    AND exercice_id = ?"
                );

                if (!$stmt) {
                    error_log('Prepare failed: ' . mysqli_error($this->conn));
                    return false;
                }

                mysqli_stmt_bind_param($stmt, "dii", $data['note'], $data['etudiant_id'], $data['exercice_id']);

            } else {
                $stmt = mysqli_prepare($this->conn, 
                    "UPDATE progression_exercice
                    SET statut = ?
                    WHERE etudiant_id = ?
                    AND exercice_id = ?"
                );

                if (!$stmt) {
                    error_log('Prepare failed: ' . mysqli_error($this->conn));
                    return false;
                }
                
                mysqli_stmt_bind_param($stmt, "sii", $data['statut'], $data['etudiant_id'], $data['exercice_id']);

            }

            $execute = mysqli_stmt_execute($stmt);

            if($execute === false){
                return false;
            }

            return $execute;
        }
    }
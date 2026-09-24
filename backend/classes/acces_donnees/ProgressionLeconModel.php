<?php
    require_once('BaseDeDonnee.php');

    class ProgressionLeconModel{
        private $conn;

        public function __construct(BaseDeDonnee $db){
            $this->conn = $db->getConn();
        }


        // progressionLecon table
        public function getProgressionLecon($id){
            $stmt = mysqli_prepare($this->conn, "SELECT * FROM progression_lecon WHERE progression_lecon_id = ?");

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

        public function getAllProgressionLecons(){
            $query = "SELECT * FROM progression_lecon";

            $result = mysqli_query($this->conn, $query);

            if (!$result) {
                error_log('Query failed: ' . mysqli_error($this->conn));
                return false;
            }

            return mysqli_fetch_all($result, MYSQLI_ASSOC);
        }

        public function getProgressionByLeconEtudiant($etudiant_id, $cours_id, $lecon_id){
            $stmt = mysqli_prepare($this->conn, 
            "SELECT *
            FROM progression_lecon
            WHERE etudiant_id = ?
            AND cours_id = ?
            AND lecon_id = ?");

            if(!$stmt){
                error_log('Prepare failed' . mysqli_error($this->conn));
                return false;
            }

            mysqli_stmt_bind_param($stmt, "iii", $etudiant_id, $cours_id, $lecon_id);
            $execute = mysqli_stmt_execute($stmt);

            if($execute === false){
                return false;
            }

            $result = mysqli_stmt_get_result($stmt);

            return mysqli_fetch_assoc($result);
        }


        public function creerProgressionLecon($data){
            $stmt = mysqli_prepare($this->conn, 
                "INSERT INTO progression_lecon(cours_id, lecon_id, etudiant_id, complete_le)
                VALUES(?, ?, ?, ?)");

            if (!$stmt) {
                error_log('Prepare failed: ' . mysqli_error($this->conn));
                return false;
            }

            mysqli_stmt_bind_param($stmt, "iiis", $data['cours_id'], $data['lecon_id'], $data['etudiant_id'], $data['complete_le']);
            $execute = mysqli_stmt_execute($stmt);

            if($execute === false){
                return false;
            }

            return mysqli_insert_id($this->conn);
        }

        public function updateProgressionLecon($data){
            if(isset($data['note'])){
                $stmt = mysqli_prepare($this->conn, 
                    "UPDATE progression_lecon
                    SET note = ?
                    WHERE etudiant_id = ?
                    AND cours_id = ?
                    AND lecon_id = ?"
                );
                if(!$stmt){
                    error_log('Prepare failed' . mysqli_error($this->conn));
                    return false;
                }

                mysqli_stmt_bind_param($stmt, "diii", $data['note'], $data['etudiant_id'], $data['cours_id'], $data['lecon_id']);
            } else {
                $stmt = mysqli_prepare($this->conn, 
                    "UPDATE progression_lecon
                    SET statut = ?,
                    complete_le = ?
                    WHERE etudiant_id = ?
                    AND cours_id = ?
                    AND lecon_id = ?"
                );

                if(!$stmt){
                    error_log('Prepare failed' . mysqli_error($this->conn));
                    return false;
                }

                mysqli_stmt_bind_param($stmt, "ssiii", $data['statut'], $data['complete_le'], $data['etudiant_id'], $data['cours_id'], $data['lecon_id']);
            }


            $execute = mysqli_stmt_execute($stmt);

            if($execute === false){
                return false;
            }

            return $execute;
        }
    }
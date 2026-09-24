<?php
    require_once('BaseDeDonnee.php');

    class ProgressionModel{
        private $conn;

        public function __construct(BaseDeDonnee $db){
            $this->conn = $db->getConn();
        }


        // progression table
        public function getProgression($id){
            $stmt = mysqli_prepare($this->conn, "SELECT * FROM progression WHERE progression_id = ?");

            if (!$stmt) {
                error_log('Prepare failed: ' . mysqli_error($this->conn));
                return false;
            }
            mysqli_stmt_bind_param($stmt, "i", $id);
            $execute = mysqli_stmt_execute($stmt);

            if(!$execute){
                return false;
            }

            $result = mysqli_stmt_get_result($stmt);

            return mysqli_fetch_assoc($result);
        }


        public function calculerTauxCompletion($formateur_id){
            $stmt = mysqli_prepare($this->conn, 
                "SELECT
                    COUNT(*) AS total,
                    SUM(p.complete_le IS NOT NULL) AS termines
                FROM inscription AS i
                INNER JOIN cours AS c ON i.cours_id = c.cours_id
                LEFT JOIN progression AS p
                    ON p.etudiant_id = i.etudiant_id
                    AND p.cours_id = i.cours_id
                WHERE c.formateur_id = ?"
            );

            if(!$stmt){
                error_log('Prepare failed: ' . mysqli_error($this->conn));
                return false;
            }

            mysqli_stmt_bind_param($stmt, "i", $formateur_id);
            $execute = mysqli_stmt_execute($stmt);
                
            if(!$execute){
                return false;
            }

            $result = mysqli_stmt_get_result($stmt);
            return mysqli_fetch_assoc($result);

        }

        public function getProgressionPerEtudiant($etudiant_id, $cours_id){
            $stmt = mysqli_prepare($this->conn,
                "SELECT *
                FROM progression
                WHERE etudiant_id = ?
                AND cours_id = ?"
            );

            if (!$stmt) {
                error_log('Prepare failed: ' . mysqli_error($this->conn));
                return false;
            }
            mysqli_stmt_bind_param($stmt, "ii", $etudiant_id, $cours_id);
            $execute = mysqli_stmt_execute($stmt);

            if(!$execute){
                return false;
            }

            $result = mysqli_stmt_get_result($stmt);

            return mysqli_fetch_assoc($result);
        }

        public function getAllProgressions(){
            $query = "SELECT * FROM progression";

            $result = mysqli_query($this->conn, $query);

            if (!$result) {
                error_log('Query failed: ' . mysqli_error($this->conn));
                return false;
            }

            return mysqli_fetch_all($result, MYSQLI_ASSOC);
        }

        public function creerProgression($data){
            $stmt = mysqli_prepare($this->conn, "INSERT INTO progression(etudiant_id, cours_id, complete_le, derniere_lecon_id, modifie_le) VALUES(?, ?, ?, ?, ?)");

            if (!$stmt) {
                error_log('Prepare failed: ' . mysqli_error($this->conn));
                return false;
            }

            mysqli_stmt_bind_param($stmt, "iisis", $data['etudiant_id'], $data['cours_id'], $data['complete_le'], $data['derniere_lecon_id'], $data['modifie_le']);
            $execute = mysqli_stmt_execute($stmt);

            if(!$execute){
                return false;
            }

            return mysqli_insert_id($this->conn);
        }

        public function updateProgression($data){
            if(isset($data['note_finale'])){
                $stmt = mysqli_prepare($this->conn,
                    "UPDATE progression
                    SET note_finale = ?
                    WHERE etudiant_id = ?
                    AND cours_id = ?"
                );

                if(!$stmt){
                    error_log('Prepare failed: ' . mysqli_error($this->conn));
                    return false;
                }

                mysqli_stmt_bind_param($stmt, "dii", $data['note_finale'], $data['etudiant_id'], $data['cours_id']);

            } else if($data['complete_le'] !== null){
                $stmt = mysqli_prepare($this->conn,
                    "UPDATE progression
                    SET complete_le = ?, derniere_lecon_id = ?
                    WHERE etudiant_id = ?
                    AND cours_id = ?"
                );

                if(!$stmt){
                    error_log('Prepare failed: ' . mysqli_error($this->conn));
                    return false;
                }
                
                mysqli_stmt_bind_param($stmt, "siii", $data['complete_le'], $data['derniere_lecon_id'], $data['etudiant_id'], $data['cours_id']);
            } else {
                $stmt = mysqli_prepare($this->conn,
                    "UPDATE progression
                    SET derniere_lecon_id = ?
                    WHERE etudiant_id = ?
                    AND cours_id = ?"
                );

                if(!$stmt){
                    error_log('Prepare failed: ' . mysqli_error($this->conn));
                    return false;
                }

                mysqli_stmt_bind_param($stmt, "iii", $data['derniere_lecon_id'], $data['etudiant_id'], $data['cours_id']);
            }

            $execute = mysqli_stmt_execute($stmt);

            if(!$execute){
                return false;
            }

            return $execute;
        }
    }
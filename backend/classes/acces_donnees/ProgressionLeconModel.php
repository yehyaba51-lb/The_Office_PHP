<?php
    require_once('BaseDeDonnee.php');
    require_once(__DIR__ . '/../acces_donnees/LeconModel.php');
    require_once(__DIR__ . '/../acces_donnees/ProgressionModel.php');

    class ProgressionLeconModel{
        private $conn;
        private $leconModel;
        private $progressionModel;

        public function __construct(BaseDeDonnee $db){
            $this->conn = $db->getConn();
            $this->leconModel = new LeconModel($db);
            $this->progressionModel = new ProgressionModel($db);
        }


        // progressionLecon table
        public function getProgressionLecon($cours_id, $lecon_id, $etudiant_id){
            $stmt = mysqli_prepare($this->conn,
                "SELECT *
                FROM progression_lecon 
                WHERE cours_id = ?
                AND lecon_id = ?
                AND etudiant_id = ?"
            );

            if (!$stmt) {
                error_log('Prepare failed: ' . mysqli_error($this->conn));
                return false;
            }

            mysqli_stmt_bind_param($stmt, "iii", $cours_id, $lecon_id, $etudiant_id);
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
                "INSERT INTO progression_lecon(cours_id, lecon_id, etudiant_id, statut)
                VALUES(?, ?, ?, 'en_cours')"
            );

            if (!$stmt) {
                error_log('Prepare failed: ' . mysqli_error($this->conn));
                return false;
            }

            mysqli_stmt_bind_param($stmt, "iii", $data['cours_id'], $data['lecon_id'], $data['etudiant_id']);
            $execute = mysqli_stmt_execute($stmt);

            if($execute === false){
                return false;
            }

            return mysqli_insert_id($this->conn);
        }

        public function etudiantsIdsAvecLeconTerminee($cours_id, $lecon_id){
            $stmt = mysqli_prepare($this->conn,
                "SELECT etudiant_id
                FROM progression_lecon
                WHERE cours_id = ?
                AND lecon_id = ?
                AND statut = 'terminee'"
            );

            if (!$stmt) {
                error_log('Prepare failed: ' . mysqli_error($this->conn));
                return false;
            }

            mysqli_stmt_bind_param($stmt, "ii", $cours_id, $lecon_id);
            $execute = mysqli_stmt_execute($stmt);

            if($execute === false){
                return false;
            }

            $result = mysqli_stmt_get_result($stmt);
            return mysqli_fetch_all($result, MYSQLI_ASSOC);
        }

        public function updateProgressionLeconStatut($cours_id, $lecon_id, $etudiant_id){
            $stmt_1 = mysqli_prepare($this->conn, 
                "UPDATE progression_lecon
                SET statut = 'terminee', complete_le = NOW()
                WHERE cours_id = ?
                AND lecon_id = ?
                AND etudiant_id = ?"
            );

            if(!$stmt_1) {
                error_log('Prepare failed: ' . mysqli_error($this->conn));
                return false;
            }

            mysqli_stmt_bind_param($stmt_1, "iii", $cours_id, $lecon_id, $etudiant_id);
            $execute = mysqli_stmt_execute($stmt_1);

            if($execute === false){
                return false;
            }

            $lecon = $this->leconModel->getLecon($lecon_id, $cours_id);

            $next_lecon_ordre = $lecon['lecon_ordre'] + 1;

            $next_lecon = $this->leconModel->getLeconByOrdre($cours_id, $next_lecon_ordre);

            if($next_lecon){
                $stmt = mysqli_prepare($this->conn, 
                    "INSERT INTO progression_lecon(cours_id, lecon_id, etudiant_id, statut)
                    VALUES(?, ?, ?, 'en_cours')"
                );
    
                if (!$stmt) {
                    error_log('Prepare failed: ' . mysqli_error($this->conn));
                    return false;
                }
    
                mysqli_stmt_bind_param($stmt, "iii", $cours_id, $next_lecon['lecon_id'], $etudiant_id);
                $execute = mysqli_stmt_execute($stmt);
    
                if($execute === false){
                    return false;
                }
                
                return ['has_next' => true, 'next_lecon_id' => $next_lecon['lecon_id']];
            } else {
                $progressionCompleteLeUpdate = $this->progressionModel->updateProgressionCompleteLe($cours_id, $etudiant_id);

                if($progressionCompleteLeUpdate === false){
                    return false;
                }

                return ['has_next' => false];
            }
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
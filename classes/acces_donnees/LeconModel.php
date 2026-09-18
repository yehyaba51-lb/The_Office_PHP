<?php
    require_once('BaseDeDonnee.php');

    class LeconModel{
        private $conn;

        public function __construct(BaseDeDonnee $db){
            $this->conn = $db->getConn();
        }

        // lecon table
        public function getLecon($id){
            $stmt = mysqli_prepare($this->conn, "SELECT * FROM lecon WHERE lecon_id = ?");

            if (!$stmt) {
                error_log('Prepare failed: ' . mysqli_error($this->conn));
                return false;
            }

            mysqli_stmt_bind_param($stmt, "i", $id);
            mysqli_stmt_execute($stmt);

            $result = mysqli_stmt_get_result($stmt);

            return mysqli_fetch_assoc($result);
        }

        public function getAllLecons(){
            $query = "SELECT * FROM lecon";

            $result = mysqli_query($this->conn, $query);

            if (!$result) {
                error_log('Query failed: ' . mysqli_error($this->conn));
                return false;
            }

            return mysqli_fetch_all($result, MYSQLI_ASSOC);
        }

        public function getLeconsByCours($cours_id){
            $stmt = mysqli_prepare($this->conn,
                "SELECT   
                    l.lecon_id AS id,
                    l.cours_id,
                    l.lecon_titre,
                    l.lecon_ordre,
                    (SELECT COUNT(*) FROM exercice AS e WHERE e.lecon_id = l.lecon_id AND e.cours_id = l.cours_id) AS exercices,
                    EXISTS(SELECT 1 FROM lecon_video AS lv WHERE lv.lecon_id = l.lecon_id AND lv.cours_id = l.cours_id) AS has_video,
                    EXISTS(SELECT 1 FROM lecon_pdf AS lp WHERE lp.lecon_id = l.lecon_id AND lp.cours_id = l.cours_id) AS has_pdf,
                    EXISTS(SELECT 1 FROM lecon_texte AS lt WHERE lt.lecon_id = l.lecon_id AND lt.cours_id = l.cours_id) AS has_texte
                FROM lecon AS l

                WHERE l.cours_id = ?");

            if (!$stmt) {
                error_log('Prepare failed: ' . mysqli_error($this->conn));
                return false;
            }

            mysqli_stmt_bind_param($stmt, "i", $cours_id);
            $execute = mysqli_stmt_execute($stmt);

            if(!$execute){
                return false;
            }

            $result = mysqli_stmt_get_result($stmt);

            return mysqli_fetch_all($result, MYSQLI_ASSOC);

        }

        public function getLeconByOrdre($cours_id, $lecon_order){
            $stmt = mysqli_prepare($this->conn, 
                "SELECT *
                FROM lecon
                WHERE cours_id = ? AND lecon_order = ?"
            );

            if (!$stmt) {
                error_log('Prepare failed: ' . mysqli_error($this->conn));
                return false;
            }

            mysqli_stmt_bind_param($stmt, "ii", $cours_id, $lecon_order);
            mysqli_stmt_execute($stmt);

            $result = mysqli_stmt_get_result($stmt);
            return mysqli_fetch_assoc($result);
        }

        public function creerLecon($data){
            $stmt = mysqli_prepare($this->conn, "INSERT INTO lecon(cours_id, lecon_titre, lecon_ordre) VALUES(?, ?, ?)");

            if (!$stmt) {
                error_log('Prepare failed: ' . mysqli_error($this->conn));
                return false;
            }

            $lecon_titre = strtolower($data['lecon_titre']);
            mysqli_stmt_bind_param($stmt, "isi", $data['cours_id'], $lecon_titre, $data['lecon_ordre']);
            mysqli_stmt_execute($stmt);

            return mysqli_insert_id($this->conn);
        }

        public function updateLecon($id, $data){
            $stmt = mysqli_prepare($this->conn, "UPDATE lecon SET lecon_titre = ?, lecon_ordre = ? WHERE lecon_id = ?");

            if (!$stmt) {
                error_log('Prepare failed: ' . mysqli_error($this->conn));
                return false;
            }
            
            $lecon_titre = strtolower($data['lecon_titre']);
            mysqli_stmt_bind_param($stmt, "sii", $lecon_titre, $data['lecon_ordre'], $id);
            return mysqli_stmt_execute($stmt);
        }

        public function supprimerLecon($id){
            $stmt = mysqli_prepare($this->conn, "DELETE FROM lecon WHERE lecon_id = ?");

            if (!$stmt) {
                error_log('Prepare failed: ' . mysqli_error($this->conn));
                return false;
            }

            mysqli_stmt_bind_param($stmt, "i", $id);
            return mysqli_stmt_execute($stmt);
        }

    }
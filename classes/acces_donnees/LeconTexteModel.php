<?php
    require_once('BaseDeDonnee.php');

    class LeconTexteModel{
        private $conn;

        public function __construct(BaseDeDonnee $db){
            $this->conn = $db->getConn();
        }


        // leconTexte table
        public function getLeconTexte($id){
            $stmt = mysqli_prepare($this->conn, "SELECT * FROM lecon_texte WHERE texte_id = ?");

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

        public function getLeconTextesByLecon($cours_id, $lecon_id){
            $stmt = mysqli_prepare($this->conn,
                "SELECT *
                FROM lecon_texte
                WHERE lecon_id = ?
                AND cours_id = ?
                ORDER BY texte_order"
            );

            if (!$stmt) {
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

        public function getAllLeconTextes(){
            $query = "SELECT * FROM lecon_texte";

            $result = mysqli_query($this->conn, $query);

            if (!$result) {
                error_log('Query failed: ' . mysqli_error($this->conn));
                return false;
            }

            return mysqli_fetch_all($result, MYSQLI_ASSOC);
        }

        public function creerLeconTexte($data){
            $stmt = mysqli_prepare($this->conn, 
                "SELECT MAX(texte_order) AS max_ordre
                FROM lecon_texte
                WHERE lecon_id = ? AND cours_id = ?"
            );

            if ($stmt === false) {
                error_log('Prepare failed: ' . mysqli_error($this->conn));
                return false;
            }

            mysqli_stmt_bind_param($stmt, "ii", $data['lecon_id'], $data['cours_id']);
            $execute = mysqli_stmt_execute($stmt);

            if($execute === false){
                return false;
            }

            $result = mysqli_stmt_get_result($stmt);
            $row = mysqli_fetch_assoc($result);

            $texte_order = $row['max_ordre'] === null ? 1 : $row['max_ordre'] + 1;

            $stmt = mysqli_prepare($this->conn, "INSERT INTO lecon_texte(lecon_id, cours_id, contenu_texte, texte_order) VALUES(?, ?, ?, ?)");

            if ($stmt === false) {
                error_log('Prepare failed: ' . mysqli_error($this->conn));
                return false;
            }

            mysqli_stmt_bind_param($stmt, "iisi", $data['lecon_id'], $data['cours_id'], $data['contenu_texte'], $texte_order);
            $execute = mysqli_stmt_execute($stmt);

            if($execute === false){
                return false;
            }

            return mysqli_insert_id($this->conn);
        }
    }
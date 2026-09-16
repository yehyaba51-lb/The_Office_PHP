<?php
    require_once('BaseDeDonnee.php');

    class CategorieModel{
        private $conn;

        public function __construct(BaseDeDonnee $db){
            $this->conn = $db->getConn();
        }

        // categorie table
        public function getCategorie($id){
            $stmt = mysqli_prepare($this->conn, "SELECT * FROM categorie WHERE categorie_id = ?");

            if (!$stmt) {
                error_log('Prepare failed: ' . mysqli_error($this->conn));
                return false;
            }

            mysqli_stmt_bind_param($stmt, "i", $id);
            mysqli_stmt_execute($stmt);

            $result = mysqli_stmt_get_result($stmt);

            return mysqli_fetch_assoc($result);
        }

        public function getAllCategories(){
            $query = "SELECT 
                        cat.categorie_id AS id,
                        cat.categorie_nom,
                        (SELECT COUNT(*) FROM cours AS co WHERE co.categorie_id = cat.categorie_id) AS cours
                    FROM categorie AS cat";

            $result = mysqli_query($this->conn, $query);

            if (!$result) {
                error_log('Query failed: ' . mysqli_error($this->conn));
                return false;
            }

            return mysqli_fetch_all($result, MYSQLI_ASSOC);
        }

        public function creerCategorie($data){
            if(empty($data['categorie_nom']) || strlen(trim($data['categorie_nom'])) < 2){
                return false;
            }

            $stmt = mysqli_prepare($this->conn, "INSERT INTO categorie(categorie_nom) VALUES(?)");

            if (!$stmt) {
                error_log('Prepare failed: ' . mysqli_error($this->conn));
                return false;
            }
            $categorie_nom = strtolower($data['categorie_nom']);
            mysqli_stmt_bind_param($stmt, "s", $categorie_nom);
            mysqli_stmt_execute($stmt);

            return mysqli_insert_id($this->conn);
        }

        public function updateCategorie($id, $data){
            if(empty($data['categorie_nom']) || strlen(trim($data['categorie_nom'])) < 2){
                return false;
            }

            $stmt = mysqli_prepare($this->conn, "UPDATE categorie SET categorie_nom = ? WHERE categorie_id = ?");

            if (!$stmt) {
                error_log('Prepare failed: ' . mysqli_error($this->conn));
                return false;
            }

            $categorie_nom = strtolower($data['categorie_nom']);
            mysqli_stmt_bind_param($stmt, "si", $categorie_nom, $id);
            return mysqli_stmt_execute($stmt);
        }

        public function supprimerCategorie($id){
            $stmt = mysqli_prepare($this->conn, "DELETE FROM categorie WHERE categorie_id = ?");

            if (!$stmt) {
                error_log('Prepare failed: ' . mysqli_error($this->conn));
                return false;
            }

            mysqli_stmt_bind_param($stmt, "i", $id);
            return mysqli_stmt_execute($stmt);
        }

    }
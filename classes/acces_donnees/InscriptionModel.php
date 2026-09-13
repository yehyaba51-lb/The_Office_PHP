<?php
    require_once('BaseDeDonnee.php');

    class InscriptionModel{
        private $conn;

        public function __construct(BaseDeDonnee $db){
            $this->conn = $db->getConn();
        }

        // inscription table
        public function getInscription($id){
            $stmt = mysqli_prepare($this->conn, "SELECT * FROM inscription WHERE inscription_id = ?");

            if (!$stmt) {
                error_log('Prepare failed: ' . mysqli_error($this->conn));
                return false;
            }

            mysqli_stmt_bind_param($stmt, "i", $id);
            mysqli_stmt_execute($stmt);

            $result = mysqli_stmt_get_result($stmt);

            return mysqli_fetch_assoc($result);
        }

        public function getInscriptionByCours($id){
            $stmt = mysqli_prepare($this->conn, 
                "SELECT 
                    i.inscription_id,
                    CONCAT(u.prenom, ' ', u.nom) AS etudiant,
                    i.inscrit_le,
                    p.cours_id,
                    p.derniere_lecon_id AS current,
                    (SELECT COUNT(*) FROM lecon AS l WHERE l.cours_id = p.cours_id ) AS total
                FROM inscription AS i
                INNER JOIN progression AS p
                i.etudiant_id = p.etudiant_id
                AND i.cours_id = p.cours_id
                INNER JOIN utilisateur AS u
                ON i.etudiant_id = u.utilisateur_id
                WHERE i.cours_id = ?");

            if (!$stmt) {
                error_log('Prepare failed: ' . mysqli_error($this->conn));
                return false;
            }

            mysqli_stmt_bind_param($stmt, "i", $id);
            mysqli_stmt_execute($stmt);

            $result = mysqli_stmt_get_result($stmt);

            return mysqli_fetch_all($result, MYSQLI_ASSOC);
        }

        public function getAllInscriptions(){
            $query = "SELECT 
                        i.inscription_id AS id,
                        i.inscrit_le,
                        CONCAT(u.prenom, ' ', u.nom) as etudiant,
                        c.cours_titre AS cours
                    FROM inscription AS i
                    INNER JOIN cours AS c
                    ON i.cours_id = c.cours_id
                    INNER JOIN utilisateur AS u
                    ON i.etudiant_id = u.utilisateur_id";

            $result = mysqli_query($this->conn, $query);

            if (!$result) {
                error_log('Query failed: ' . mysqli_error($this->conn));
                return false;
            }

            return mysqli_fetch_all($result, MYSQLI_ASSOC);
        }

        public function creerInscription($data){
            $stmt = mysqli_prepare($this->conn, "INSERT INTO inscription(etudiant_id, cours_id, note_finale) VALUES(?, ?, null)");

            if (!$stmt) {
                error_log('Prepare failed: ' . mysqli_error($this->conn));
                return false;
            }

            mysqli_stmt_bind_param($stmt, "ii", $data['etudiant_id'], $data['cours_id']);
            mysqli_stmt_execute($stmt);

            return mysqli_insert_id($this->conn);
        }

        public function supprimerInscription($id){
            $stmt = mysqli_prepare($this->conn, "DELETE FROM inscription WHERE inscription_id = ?");

            if (!$stmt) {
                error_log('Prepare failed: ' . mysqli_error($this->conn));
                return false;
            }

            mysqli_stmt_bind_param($stmt, "i", $id);
            return mysqli_stmt_execute($stmt);
        }
    }
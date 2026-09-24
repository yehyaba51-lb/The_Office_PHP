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
            $execute = mysqli_stmt_execute($stmt);

            if(!$execute){
                return false;
            }

            $result = mysqli_stmt_get_result($stmt);

            return mysqli_fetch_assoc($result);
        }

        public function getInscriptionByCours($id){
            $stmt = mysqli_prepare($this->conn, 
                "SELECT 
                    i.inscription_id AS id,
                    CONCAT(u.prenom, ' ', u.nom) AS etudiant,
                    DATE(i.inscrit_le) AS inscrit_le,
                    (SELECT COUNT(*) FROM progression_lecon AS pl
                    WHERE pl.cours_id = i.cours_id
                        AND pl.etudiant_id = i.etudiant_id
                        AND statut = 'terminee') AS current,
                    i.note_finale,
                    (SELECT COUNT(*) FROM lecon AS l WHERE l.cours_id = i.cours_id ) AS total
                FROM inscription AS i
                INNER JOIN utilisateur AS u
                ON i.etudiant_id = u.utilisateur_id
                WHERE i.cours_id = ?");

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

            return mysqli_fetch_all($result, MYSQLI_ASSOC);
        }

        public function getInscriptionByFormateur($formateur_id){
            $stmt = mysqli_prepare($this->conn, 
                "SELECT 
                    i.inscription_id AS id,
                    CONCAT(u.prenom, ' ', u.nom) AS etudiant,
                    c.cours_titre,
                    DATE(i.inscrit_le) AS inscrit_le,
                    i.note_finale,
                    (SELECT COUNT(*) FROM progression_lecon AS pl
                    WHERE pl.cours_id = c.cours_id
                        AND pl.etudiant_id = i.etudiant_id
                        AND statut = 'terminee') AS current,
                    (SELECT COUNT(*) FROM lecon AS l WHERE l.cours_id = c.cours_id) AS total
                FROM inscription AS i
                INNER JOIN cours AS c
                ON i.cours_id = c.cours_id
                INNER JOIN utilisateur AS u
                ON i.etudiant_id = u.utilisateur_id
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
            return mysqli_fetch_all($result, MYSQLI_ASSOC);
        }

        public function compterEtudiants($formateur_id){
            $stmt = mysqli_prepare($this->conn, 
                "SELECT 
                    COUNT(*) AS total,
                    SUM(MONTH(i.inscrit_le) = MONTH(CURRENT_DATE()) 
                        AND YEAR(i.inscrit_le) = YEAR(CURRENT_DATE())) AS ce_mois
                FROM inscription AS i
                INNER JOIN cours AS c
                ON i.cours_id = c.cours_id
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
            $execute = mysqli_stmt_execute($stmt);
            
            if(!$execute){
                return false;
            }
            return mysqli_insert_id($this->conn);
        }

        public function supprimerInscription($id){
            $stmt = mysqli_prepare($this->conn, "DELETE FROM inscription WHERE inscription_id = ?");

            if (!$stmt) {
                error_log('Prepare failed: ' . mysqli_error($this->conn));
                return false;
            }

            mysqli_stmt_bind_param($stmt, "i", $id);
            $execute = mysqli_stmt_execute($stmt);

            if(!$execute){
                return false;
            }

            return $execute;
        }
    }
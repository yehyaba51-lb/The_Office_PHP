<?php
    require_once('BaseDeDonnee.php');

    class SoumissionModel{
        private $conn;

        public function __construct(BaseDeDonnee $db){
            $this->conn = $db->getConn();
        }


        // soumission table
        public function getSoumission($id){
            $stmt = mysqli_prepare($this->conn, "SELECT * FROM soumission WHERE soumission_id = ?");

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

        public function getAllSoumissions(){
            $query = "SELECT * FROM soumission";

            $result = mysqli_query($this->conn, $query);

            if (!$result) {
                error_log('Query failed: ' . mysqli_error($this->conn));
                return false;
            }

            return mysqli_fetch_all($result, MYSQLI_ASSOC);
        }

        public function getSoumissionsByEtudiant($etudiant_id){
            $stmt = mysqli_prepare($this->conn, 
                "SELECT *
                FROM soumission
                WHERE etudiant_id = ?"
            );

            if(!$stmt){
                error_log('Prepare failed' . mysqli_error($this->conn));
                return false;
            }

            mysqli_stmt_bind_param($stmt, "i", $etudiant_id);
            $execute = mysqli_stmt_execute($stmt);

            if(!$execute){
                return false;
            }

            $result = mysqli_stmt_get_result($stmt);

            return mysqli_fetch_all($result, MYSQLI_ASSOC);
        }

        public function compterSoumissionsNonCorrigees($formateur_id){
            $stmt = mysqli_prepare($this->conn, 
                "SELECT 
                    COUNT(*) AS total,
                    SUM(MONTH(s.soumis_le) = MONTH(CURRENT_DATE()) 
                        AND YEAR(s.soumis_le) = YEAR(CURRENT_DATE())) AS ce_mois
                FROM soumission AS s
                INNER JOIN question AS q
                ON s.question_id = q.question_id
                INNER JOIN exercice AS e
                ON q.exercice_id = e.exercice_id
                INNER JOIN cours AS c
                ON e.cours_id = c.cours_id
                WHERE c.formateur_id = ? AND s.corrige_le IS NULL"
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


        public function getSoumissionByEtudiantQuestion($etudiant_id, $question_id){
            $stmt = mysqli_prepare($this->conn, 
                "SELECT * FROM soumission WHERE etudiant_id = ? AND question_id = ?"
            );

            if(!$stmt){
                error_log('Prepare failed: ' . mysqli_error($this->conn));
                return false;
            }

            mysqli_stmt_bind_param($stmt, "ii", $etudiant_id, $question_id);
            $execute = mysqli_stmt_execute($stmt);

            if(!$execute){
                return false;
            }

            $result = mysqli_stmt_get_result($stmt);
            return mysqli_fetch_assoc($result);
        }


        public function creerSoumission($data){
            $stmt = mysqli_prepare($this->conn, 
            "INSERT INTO soumission(etudiant_id, question_id, soumission_reponse, url_fichier, soumis_le)
            VALUES(?, ?, ?, ?, ?)");

            if (!$stmt) {
                error_log('Prepare failed: ' . mysqli_error($this->conn));
                return false;
            }

            mysqli_stmt_bind_param($stmt, "iisss", $data['etudiant_id'], $data['question_id'], $data['soumission_reponse'], $data['url_fichier'], $data['soumis_le']);
            $execute = mysqli_stmt_execute($stmt);

            if(!$execute){
                return false;
            }

            return mysqli_insert_id($this->conn);
        }

        public function getSoumissionDashboard($formateur_id){
            $stmt = mysqli_prepare($this->conn, 
                "SELECT 
                    e.exercice_titre,
                    s.soumis_le,
                    CONCAT(u.prenom, ' ',  u.nom) AS etudiant,
                    s.soumission_id
                FROM soumission AS s
                INNER JOIN question AS q
                ON s.question_id = q.question_id
                INNER JOIN exercice AS e
                ON q.exercice_id = e.exercice_id
                INNER JOIN cours AS c
                ON e.cours_id = c.cours_id
                INNER JOIN utilisateur AS u
                ON s.etudiant_id = u.utilisateur_id
                WHERE c.formateur_id = ? AND s.corrige_le IS NULL"
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

        public function getSoumissionFormateur($formateur_id){
            $stmt = mysqli_prepare($this->conn, 
                "SELECT
                    s.soumission_id AS id,
                    CONCAT(u.prenom, ' ', u.nom) AS etudiant,
                    c.cours_titre,
                    q.question_id,
                    q.texte_question,
                    e.exercice_titre,
                    s.soumis_le,
                    q.question_type,
                    s.corrige_le,
                    s.url_fichier,
                    s.soumission_reponse,
                    s.note,
                    s.commentaire
                FROM soumission AS s
                INNER JOIN question AS q
                ON q.question_id = s.question_id
                INNER JOIN exercice AS e
                ON e.exercice_id = q.exercice_id
                INNER JOIN cours AS c
                ON c.cours_id = e.cours_id
                INNER JOIN utilisateur AS u
                ON u.utilisateur_id = s.etudiant_id
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

        public function corrigerSoumission($id, $data){
            if($data['note'] !== null && $data['note'] !== '' && (!is_numeric($data['note']) || $data['note'] < 0 || $data['note'] > 20)){
                return false;
            }

            $stmt = mysqli_prepare($this->conn,
                "UPDATE soumission AS s
                INNER JOIN question AS q ON q.question_id = s.question_id
                INNER JOIN exercice AS e ON e.exercice_id = q.exercice_id
                INNER JOIN cours AS c ON c.cours_id = e.cours_id
                SET s.note = ?, s.commentaire = ?, s.corrige_le = NOW(), s.corrige_par = ?
                WHERE s.soumission_id = ? AND c.formateur_id = ?"
            );

            if (!$stmt) {
                error_log('Prepare failed: ' . mysqli_error($this->conn));
                return false;
            }

            mysqli_stmt_bind_param($stmt, "dsiii", $data['note'], $data['commentaire'], $data['corrige_par'], $id, $data['corrige_par']);
            $execute = mysqli_stmt_execute($stmt);

            if(!$execute){
                return false;
            }

            return $execute;
        }


        public function resoumettre($soumission_id, $data){
            $stmt = mysqli_prepare($this->conn, 
            "UPDATE soumission
            SET soumission_reponse = ?,
            url_fichier = ?,
            soumis_le = NOW(),
            corrige_le = NULL,
            corrige_par = NULL,
            note = NULL,
            commentaire = NULL
            WHERE soumission_id = ?");

            if (!$stmt) {
                error_log('Prepare failed: ' . mysqli_error($this->conn));
                return false;
            }

            mysqli_stmt_bind_param($stmt, "ssi", $data['soumission_reponse'], $data['url_fichier'], $soumission_id);

            $execute = mysqli_stmt_execute($stmt);

            if(!$execute){
                return false;
            }

            return $execute;
        }
    }
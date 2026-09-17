<?php
require_once('BaseDeDonnee.php');

class CoursModel
{
    private $conn;

    public function __construct(BaseDeDonnee $db)
    {
        $this->conn = $db->getConn();
    }

    // cours table
    public function getCours($id)
    {
        $stmt = mysqli_prepare($this->conn, 
            "SELECT
                c.cours_id,
                c.cours_titre,
                c.description,
                CONCAT(u.prenom, ' ', u.nom) AS formateur,
                (SELECT COUNT(*) FROM exercice AS e WHERE e.cours_id = c.cours_id) AS exercices,
                (SELECT COUNT(*) FROM lecon AS l WHERE l.cours_id = c.cours_id ) AS lecons
            FROM cours AS c
            INNER JOIN utilisateur AS u
            ON c.formateur_id = u.utilisateur_id
            WHERE c.cours_id = ?");

        if (!$stmt) {
            error_log('Prepare failed: ' . mysqli_error($this->conn));
            return false;
        }

        mysqli_stmt_bind_param($stmt, "i", $id);
        mysqli_stmt_execute($stmt);

        $result = mysqli_stmt_get_result($stmt);

        if (!$result) {
            error_log('Query failed: ' . mysqli_error($this->conn));
            return false;
        }

        return mysqli_fetch_assoc($result);
    }

    public function getAllCours()
    {
        $query = "SELECT
                    co.cours_id AS id,
                    co.cours_titre,
                    co.cours_titre AS titre,
                    co.description,
                    ca.categorie_nom AS categorie,
                    CONCAT(u.prenom, ' ', u.nom) AS formateur,
                    co.formateur_id,
                    co.categorie_id,
                    (SELECT COUNT(*) FROM lecon AS l WHERE l.cours_id = co.cours_id) AS lecons
                FROM cours AS co
                LEFT JOIN categorie AS ca
                ON co.categorie_id = ca.categorie_id
                LEFT JOIN utilisateur AS u
                ON co.formateur_id = u.utilisateur_id";

        $result = mysqli_query($this->conn, $query);

        if(!$result){
            error_log('Query failed'. mysqli_error($this->conn));
            return false;
        }

        return mysqli_fetch_all($result, MYSQLI_ASSOC);
    }

    public function getCoursByFormateur($formateur_id)
    {
        $stmt = mysqli_prepare($this->conn, "SELECT * FROM cours WHERE formateur_id = ?");

        if (!$stmt) {
            error_log('Prepare failed: ' . mysqli_error($this->conn));
            return false;
        }

        mysqli_stmt_bind_param($stmt, "i", $formateur_id);
        mysqli_stmt_execute($stmt);

        $result = mysqli_stmt_get_result($stmt);

        return mysqli_fetch_all($result, MYSQLI_ASSOC);
    }

    public function getCoursByEtudiant($etudiant_id)
    {
        $stmt = mysqli_prepare($this->conn, 
            "SELECT
                c.cours_id,
                c.cours_titre,
                c.description,
                c.formateur_id,
                c.categorie_id,
                c.cree_le,
                c.url_image
            FROM cours AS c 
            INNER JOIN inscription AS i
            ON c.cours_id = i.cours_id
            WHERE i.etudiant_id = ?"
        );

        if (!$stmt) {
            error_log('Prepare failed: ' . mysqli_error($this->conn));
            return false;
        }

        mysqli_stmt_bind_param($stmt, "i", $etudiant_id);
        mysqli_stmt_execute($stmt);

        $result = mysqli_stmt_get_result($stmt);

        return mysqli_fetch_all($result, MYSQLI_ASSOC);
    }

    public function getNombreCours($formateur_id){
        $stmt = mysqli_prepare($this->conn, 
            "SELECT
                COUNT(*) AS total,
                SUM(MONTH(c.cree_le) = MONTH(CURRENT_DATE()) 
                    AND YEAR(c.cree_le) = YEAR(CURRENT_DATE())) AS ce_mois
            FROM cours AS c
            WHERE formateur_id = ?"
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

    public function getEtudiantsByCours($cours_id)
    {
        $stmt = mysqli_prepare($this->conn,
            "SELECT 
                u.utilisateur_id,
                u.prenom,
                u.nom,
                u.email,
                u.role,
                u.cree_le
            FROM cours AS c 
            INNER JOIN inscription AS i
            ON c.cours_id = i.cours_id
            INNER JOIN utilisateur AS u
            ON i.etudiant_id = u.utilisateur_id
            WHERE i.cours_id = ?"
        );

        if (!$stmt) {
            error_log('Prepare failed: ' . mysqli_error($this->conn));
            return false;
        }

        mysqli_stmt_bind_param($stmt, "i", $cours_id);
        mysqli_stmt_execute($stmt);

        $result = mysqli_stmt_get_result($stmt);

        return mysqli_fetch_all($result, MYSQLI_ASSOC);
    }

    public function creerCours($data)
    {
        if(empty($data['cours_titre']) || strlen(trim($data['cours_titre'])) < 2 || !preg_match('/^[a-zà-ÿ][a-zà-ÿ0-9\' :\-]*$/u', $data['cours_titre'])){
            return false;
        }
        if(empty($data['formateur_id']) || $data['formateur_id'] === ""){
            return false;
        }
        if(empty($data['categorie_id']) || $data['categorie_id'] === ""){
            return false;
        }

        $stmt = mysqli_prepare($this->conn, "INSERT INTO cours(cours_titre, formateur_id, categorie_id) VALUES(?, ?, ?)");

        if (!$stmt) {
            error_log('Prepare failed: ' . mysqli_error($this->conn));
            return false;
        }

        $cours_titre = strtolower($data['cours_titre']);
        mysqli_stmt_bind_param($stmt, "sii", $cours_titre, $data['formateur_id'], $data['categorie_id']);
        mysqli_stmt_execute($stmt);

        return mysqli_insert_id($this->conn);
    }

    public function updateCoursByAdmin($id, $data)
    {
        if(empty($data['cours_titre']) || strlen(trim($data['cours_titre'])) < 5 || !preg_match('/^[a-zA-ZÀ-ÿ0-9\' :\-]*$/u', $data['cours_titre'])){
            return false;
        }
        if(empty($data['formateur_id']) || $data['formateur_id'] === ""){
            return false;
        }
        if(empty($data['categorie_id']) || $data['categorie_id'] === ""){
            return false;
        }

        $stmt = mysqli_prepare($this->conn, "UPDATE cours SET cours_titre = ?, formateur_id = ?, categorie_id = ? WHERE cours_id = ?");

        if (!$stmt) {
            error_log('Prepare failed: ' . mysqli_error($this->conn));
            return false;
        }

        $cours_titre = strtolower($data['cours_titre']);
        mysqli_stmt_bind_param($stmt, "siii", $cours_titre, $data['formateur_id'], $data['categorie_id'], $id);
        return mysqli_stmt_execute($stmt);
    }

    public function supprimerCours($id)
    {
        $stmt = mysqli_prepare($this->conn, "DELETE FROM cours WHERE cours_id = ?");

        if (!$stmt) {
            error_log('Prepare failed: ' . mysqli_error($this->conn));
            return false;
        }

        mysqli_stmt_bind_param($stmt, "i", $id);
        return mysqli_stmt_execute($stmt);
    }
}

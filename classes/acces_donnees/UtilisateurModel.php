<?php
    require_once('BaseDeDonnee.php');

    class UtilisateurModel{
        private $conn;

        public function __construct(BaseDeDonnee $db){
            $this->conn = $db->getConn();
        }

        // utilisateur table
        public function getUtilisateur($id)
        {
            $stmt = mysqli_prepare($this->conn, "SELECT * FROM utilisateur WHERE utilisateur_id = ?");

            if (!$stmt) {
                error_log('Prepare failed: ' . mysqli_error($this->conn));
                return false;
            }

            mysqli_stmt_bind_param($stmt, "i", $id);
            mysqli_stmt_execute($stmt);

            $result = mysqli_stmt_get_result($stmt);


            return mysqli_fetch_assoc($result);
        }

        public function getUtilisateurByEmail($email){
            $stmt = mysqli_prepare($this->conn, "SELECT * FROM utilisateur WHERE email = ?");

            if (!$stmt) {
                error_log('Prepare failed: ' . mysqli_error($this->conn));
                return false;
            }

            mysqli_stmt_bind_param($stmt, "s", $email);
            mysqli_stmt_execute($stmt);

            $result = mysqli_stmt_get_result($stmt);

            return mysqli_fetch_assoc($result);

        }

        public function getAllUtilisateurs(){
            $query = "SELECT * FROM utilisateur";

            $result = mysqli_query($this->conn, $query);

            if (!$result) {
                error_log('Query failed: ' . mysqli_error($this->conn));
                return false;
            }

            return mysqli_fetch_all($result, MYSQLI_ASSOC);
        }

        public function getFormateurs(){
            $stmt = mysqli_prepare($this->conn, 
                "SELECT utilisateur_id AS id, prenom, nom FROM utilisateur WHERE role = 'Formateur'"
            );

            if(!$stmt){
                error_log('Prepare failed: ' . mysqli_error($this->conn));
                return false;
            }

            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);

            return mysqli_fetch_all($result, MYSQLI_ASSOC);
        }

        public function genererMotDePasse($longueur = 12){
            $caracteres = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789!@#$%^&*';
            $mot_de_passe = '';

            for($i = 0; $i < $longueur; $i++){
                $mot_de_passe .= $caracteres[random_int(0, strlen($caracteres) - 1)];
            }

            return $mot_de_passe;
        }

        public function changerPremiereConnexion($id){
            $stmt = mysqli_prepare($this->conn,
                "UPDATE utilisateur
                SET premiere_connexion = FALSE
                WHERE utilisateur_id = ?"
            );
            
            if(!$stmt){
                error_log('Prepare failed: ' . mysqli_error($this->conn));
                return false;
            }

            mysqli_stmt_bind_param($stmt, "i", $id);
            return mysqli_stmt_execute($stmt);
        }

        public function changerPassword($id, $data){
            if(empty($data['password']) || !preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/', $data['password'])){
                return false;
            }
            $stmt = mysqli_prepare($this->conn, 
                "UPDATE utilisateur
                SET mot_de_passe = ?, premiere_connexion = FALSE
                WHERE utilisateur_id = ?"
            );

            if(!$stmt){
                error_log('Prepare failed: ' . mysqli_error($this->conn));
                return false;
            }

            $mot_de_passe_hash = password_hash($data['password'], PASSWORD_DEFAULT);
            mysqli_stmt_bind_param($stmt, "si", $mot_de_passe_hash, $id);
            return mysqli_stmt_execute($stmt);
        }
    
        public function creerUtilisateur($data)
        {
            if(empty($data['prenom']) || strlen(trim($data['prenom'])) < 2 || !preg_match('/^[A-Za-z\'\-\s]+$/', $data['prenom'])){
                return false;
            }

            if(empty($data['nom']) || strlen(trim($data['nom'])) < 2 || !preg_match('/^[a-zA-Z]+$/', $data['nom'])){
                return false;
            }

            if(empty($data['email']) || !filter_var($data['email'], FILTER_VALIDATE_EMAIL)){
                return false;
            };

            if(empty($data['role']) || $data['role'] === ""){
                return false;
            }

            $mot_de_passe = $this->genererMotDePasse();
            
            $mot_de_passe_hash = password_hash($mot_de_passe, PASSWORD_DEFAULT);

            $stmt = mysqli_prepare($this->conn, "INSERT INTO utilisateur(prenom, nom, email, mot_de_passe, role) VALUES(?, ?, ?, ?, ?)");
            
            if (!$stmt) {
                error_log('Prepare failed: ' . mysqli_error($this->conn));
                return false;
            }
            
            $prenom = strtolower($data['prenom']);
            $nom = strtolower($data['nom']);
            $email = strtolower($data['email']);
            mysqli_stmt_bind_param($stmt, 'sssss', $prenom, $nom, $email, $mot_de_passe_hash, $data['role']);
            mysqli_stmt_execute($stmt);

            return [
                'id' => mysqli_insert_id($this->conn),
                'mot_de_passe' => $mot_de_passe
            ];
        }

        public function updateUtilisateur($id, $data){
            if(empty($data['prenom']) || strlen(trim($data['prenom'])) < 2 || !preg_match('/^[A-Za-z\'\-\s]+$/', $data['prenom'])){
                return false;
            }

            if(empty($data['nom']) || strlen(trim($data['nom'])) < 2 || !preg_match('/^[a-zA-Z]+$/', $data['nom'])){
                return false;
            }

            if(empty($data['email']) || !filter_var($data['email'], FILTER_VALIDATE_EMAIL)){
                return false;
            };

            if(empty($data['role']) || $data['role'] === ""){
                return false;
            }
            $stmt = mysqli_prepare($this->conn, "UPDATE utilisateur SET prenom = ?, nom = ?, email = ? WHERE utilisateur_id  = ?");

            if (!$stmt) {
                error_log('Prepare failed: ' . mysqli_error($this->conn));
                return false;
            }

            $prenom = strtolower($data['prenom']);
            $nom = strtolower($data['nom']);
            $email = strtolower($data['email']);
            mysqli_stmt_bind_param($stmt, "sssi", $prenom, $nom, $email, $id);
            return mysqli_stmt_execute($stmt);
            
        }

        public function reinitialiserMotDePasse($id){
            $mot_de_passe = $this->genererMotDePasse();
            $mot_de_passe_hash = password_hash($mot_de_passe, PASSWORD_DEFAULT);

            $stmt = mysqli_prepare($this->conn, 
                "UPDATE utilisateur
                SET mot_de_passe = ?
                WHERE utilisateur_id = ?"
            );

            if(!$stmt){
                error_log('Prepare failed: ' . mysqli_error($this->conn));
                return false;
            }

            mysqli_stmt_bind_param($stmt, "si", $mot_de_passe_hash, $id);
            mysqli_stmt_execute($stmt);

            return [
                'id' => mysqli_insert_id($this->conn),
                'mot_de_passe' => $mot_de_passe
            ];
        }

        public function supprimerUtilisateur($id){
            $stmt = mysqli_prepare($this->conn, "DELETE FROM utilisateur WHERE utilisateur_id = ?");

            if (!$stmt) {
                error_log('Prepare failed: ' . mysqli_error($this->conn));
                return false;
            }

            mysqli_stmt_bind_param($stmt, "i", $id);
            return mysqli_stmt_execute($stmt);
        }
    }
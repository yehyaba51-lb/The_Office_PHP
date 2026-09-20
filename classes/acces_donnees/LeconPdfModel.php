<?php
    require_once('BaseDeDonnee.php');

    class LeconPdfModel{
        private $conn;

        public function __construct(BaseDeDonnee $db){
            $this->conn = $db->getConn();
        }

        
        // leconPDF table
        public function getLeconPdf($id){
            $stmt = mysqli_prepare($this->conn, "SELECT * FROM lecon_pdf WHERE pdf_id = ?");

            if (!$stmt) {
                error_log('Prepare failed: ' . mysqli_error($this->conn));
                return false;
            }

            mysqli_stmt_bind_param($stmt, "i", $id);
            mysqli_stmt_execute($stmt);

            $result = mysqli_stmt_get_result($stmt);

            return mysqli_fetch_assoc($result);
        }

        public function getLeconPdfsByLecon($lecon_id, $cours_id){
            $stmt = mysqli_prepare($this->conn,
                "SELECT *
                FROM lecon_pdf
                WHERE lecon_id = ?
                AND cours_id = ?
                ORDER BY pdf_order"
            );

            if (!$stmt) {
                error_log('Prepare failed: ' . mysqli_error($this->conn));
                return false;
            }

            mysqli_stmt_bind_param($stmt, "ii", $lecon_id, $cours_id);
            mysqli_stmt_execute($stmt);

            $result = mysqli_stmt_get_result($stmt);

            return mysqli_fetch_all($result, MYSQLI_ASSOC);
        }

        public function getAllLeconPdfs(){
            $query = "SELECT * FROM lecon_pdf";

            $result = mysqli_query($this->conn, $query);

            if (!$result) {
                error_log('Query failed: ' . mysqli_error($this->conn));
                return false;
            }

            return mysqli_fetch_all($result, MYSQLI_ASSOC);
        }

        public function creerLeconPdf($data){
            $stmt = mysqli_prepare($this->conn, "INSERT INTO lecon_pdf(lecon_id, cours_id, url_pdf, pdf_order) VALUES(?, ?, ?, ?)");

            if (!$stmt) {
                error_log('Prepare failed: ' . mysqli_error($this->conn));
                return false;
            }

            mysqli_stmt_bind_param($stmt, "iisi", $data['lecon_id'], $data['cours_id'], $data['url_pdf'], $data['pdf_order']);
            mysqli_stmt_execute($stmt);

            return mysqli_insert_id($this->conn);
        }
    }
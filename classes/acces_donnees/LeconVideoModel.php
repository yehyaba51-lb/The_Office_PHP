<?php
    require_once('BaseDeDonnee.php');

    class LeconVideoModel{
        private $conn;

        public function __construct(BaseDeDonnee $db){
            $this->conn = $db->getConn();
        }


        // leconVideo table
        public function getLeconVideo($id){
            $stmt = mysqli_prepare($this->conn, "SELECT * FROM lecon_video WHERE video_id = ?");

            if (!$stmt) {
                error_log('Prepare failed: ' . mysqli_error($this->conn));
                return false;
            }

            mysqli_stmt_bind_param($stmt, "i", $id);
            mysqli_stmt_execute($stmt);

            $result = mysqli_stmt_get_result($stmt);

            return mysqli_fetch_assoc($result);
        }

        public function getLeconVideosByLecon($cours_id, $lecon_id){
            $stmt = mysqli_prepare($this->conn,
                "SELECT *
                FROM lecon_video
                WHERE lecon_id = ?
                AND cours_id = ?
                ORDER BY video_order"
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

        public function getAllLeconVideos(){
            $query = "SELECT * FROM lecon_video";

            $result = mysqli_query($this->conn, $query);

            if (!$result) {
                error_log('Query failed: ' . mysqli_error($this->conn));
                return false;
            }

            return mysqli_fetch_all($result, MYSQLI_ASSOC);
        }

        public function creerLeconVideo($data){
            $stmt = mysqli_prepare($this->conn, "INSERT INTO lecon_video(lecon_id, cours_id, url_video, video_order) VALUES(?, ?, ?, ?)");

            if (!$stmt) {
                error_log('Prepare failed: ' . mysqli_error($this->conn));
                return false;
            }
            
            mysqli_stmt_bind_param($stmt, "iisi", $data['lecon_id'], $data['cours_id'], $data['url_video'], $data['video_order']);
            mysqli_stmt_execute($stmt);

            return mysqli_insert_id($this->conn);
        }
    }
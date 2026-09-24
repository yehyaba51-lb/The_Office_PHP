<?php
    require_once('ContenuLecon.php');

    class LeconVideo extends ContenuLecon{
        private $video_id;
        private $url_video;
        private $video_ordre;
        private $duree;

        public function getVideoId(){
            return $this->video_id;
        }

        public function setVideoId($video_id){
            $this->video_id = $video_id;
        }

        public function getUrlVideo(){
            return $this->url_video;
        }

        public function setUrlVideo($url_video){
            $this->url_video = $url_video;
        }

        public function getVideoOrdre(){
            return $this->video_ordre;
        }

        public function setVideoOrdre($video_ordre){
            $this->video_ordre = $video_ordre;
        }

        public function getDuree(){
            return $this->duree;
        }

        public function setDuree($duree){
            $this->duree = $duree;
        }

    }
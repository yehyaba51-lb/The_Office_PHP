<?php
    require_once('ContenuLecon.php');

    class LeconPdf extends ContenuLecon{
        private $pdf_id;
        private $url_pdf;
        private $pdf_ordre;


        public function getPdfId(){
            return $this->pdf_id;
        }

        public function setPdfId($pdf_id){
            $this->pdf_id = $pdf_id;
        }

        public function getUrlPdf(){
            return $this->url_pdf;
        }

        public function setUrlPdf($url_pdf){
            $this->url_pdf = $url_pdf;
        }

        public function getPdfOrdre(){
            return $this->pdf_ordre;
        }

        public function setPdfOrdre($pdf_ordre){
            $this->pdf_ordre = $pdf_ordre;
        }

    }
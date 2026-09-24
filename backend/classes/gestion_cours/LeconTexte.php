<?php
    require_once('ContenuLecon.php');

    class LeconTexte extends ContenuLecon{
        private $texte_id;
        private $contenu_texte;
        private $texte_ordre;

        public function getTexteId(){
            return $this->texte_id;
        }

        public function setTexteId($texte_id){
            $this->texte_id = $texte_id;
        }

        public function getContenuTexte(){
            return $this->contenu_texte;
        }

        public function setContenuTexte($contenu_texte){
            $this->contenu_texte = $contenu_texte;
        }

        public function getTexteOrdre(){
            return $this->texte_ordre;
        }

        public function setTexteOrdre($texte_ordre){
            $this->texte_ordre = $texte_ordre;
        }

    }
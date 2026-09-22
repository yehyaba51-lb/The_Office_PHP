<?php
    class FichierValidateur{

        public function getMimeType($file_path){
            $finfo = new finfo(FILEINFO_MIME_TYPE);

            return $finfo->file($file_path);
        }


        public function valider($file, $types_accepted, $TAILLE_MAX){
            if($file['size'] > $TAILLE_MAX){
                return false;
            }

            $mime = $this->getMimeType($file['tmp_name']);

            

            if(!in_array($mime, $types_accepted)){
                return false;
            }

            return true;
        }


        public function genererNomFichier($original_name, $sender_name, $destination_path){
            $extension = pathinfo($original_name, PATHINFO_EXTENSION);

            $base_name = pathinfo($original_name, PATHINFO_FILENAME);


            $name = $base_name . '_' . $sender_name;

            $name = preg_replace('/[^A-Za-z0-9_-]/', '_', $name);

            $final_name = $name . '.' . $extension;
            $i = 1;

            while(file_exists($destination_path . '/' . $final_name)){
                $final_name = $name . '(' . $i . ').' . $extension;

                $i++;
            }

            return $final_name;
        }
    }
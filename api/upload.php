<?php
    require_once(__DIR__ . '/../vendor/autoload.php');
    $dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/..');
    $dotenv->load();

    header('Access-Control-Allow-Origin: ' . $_ENV['FRONTEND_URL']);
    header('Access-Control-Allow-Credentials: true');
    header('Access-Control-Allow-Headers: Content-Type');
    header('Content-Type: application/json');
    header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');


    require_once(__DIR__ . '/../classes/gestion_cours/CoursManager.php');

    $validateur = new FichierValidateur();
    $manager = new CoursManager();

    if($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
        http_response_code(200);
        exit;
    } else if($_SERVER['REQUEST_METHOD'] === 'POST'){
        if(!isset($_FILES['fichier'])){
            http_response_code(400);
            echo json_encode(['error' => 'Image pas envoyé']);
            exit;
        } else {
            $image = $_FILES['fichier'];

            if(empty($image)){
                http_response_code(400);
                echo json_encode(['error' => "Pas d'image uploadé"]);
                exit;
            } else {
                $valide = $validateur->valider($image);

                if($valide === false){
                    http_response_code(400);
                    echo json_encode(['error' => 'Fichier invalide']);
                    exit;
                }

                $dossierUploads = __DIR__ . '/../../uploads/thumbnails/';


                if(!is_dir($dossierUploads)){
                    mkdir($dossierUploads, 0777, true);
                }

                $nomFichier  = $validateur->genererNomFichier($image['name'], 'cours', $dossierUploads);

                $chemain_final = $dossierUploads . '/' . $nomFichier;

                $deplace = move_uploaded_file($image['tmp_name'], $chemain_final);

                if(!$deplace){
                     http_response_code(500);
                    echo json_encode(['error' => 'Impossible de sauvegarder le fichier']);
                    exit;
                }

                http_response_code(201);
                echo json_encode(['url' => 'uploads/' . $nomFichier]);

                $result = $manager->updateImage();
            }
        }
    } else {
        http_response_code(405);
        echo json_encode(['error' => 'Méthode non autorisée']);
    }

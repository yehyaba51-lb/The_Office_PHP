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
    require_once(__DIR__ . '/../classes/support/FichierValidateur.php');
    require_once(__DIR__ . '/../classes/authentification/Authentification.php');

    

    if($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
        http_response_code(200);
        exit;
    }

    $validateur = new FichierValidateur();
    $manager = new CoursManager();
    $auth = new Authentification();

    
    if($_SERVER['REQUEST_METHOD'] === 'POST'){
        if(!isset($_FILES['fichier'])){
            http_response_code(400);
            echo json_encode(['error' => 'Aucune image envoyée']);
            exit;
        } else {
            if(!isset($_GET['id'])){
                http_response_code(400);
                echo json_encode(['error' => 'Id manquant']);
                exit;
            } else {
                if(!$auth->verifierRole('Formateur')){
                    http_response_code(403);
                    echo json_encode(['error' => 'Accès refusé']);
                    exit;
                }

                $image = $_FILES['fichier'];
                $cours_id = $_GET['id'];
    
                    $valide = $validateur->valider($image, ['image/jpeg', 'image/png'], 5 * 1024 * 1024);

                    if(is_array($valide) && isset($valide['error'])){
                        http_response_code(400);
                        echo json_encode($valide);
                        exit;
                    }
    
                    $dossierUploads = __DIR__ . '/../../uploads/thumbnails/';
    
    
                    if(!is_dir($dossierUploads)){
                        mkdir($dossierUploads, 0777, true);
                    }
    
                    $nomFichier  = $validateur->genererNomFichier($image['name'], 'cours', $dossierUploads);
    
                    $chemin_final = $dossierUploads . '/' . $nomFichier;
    
                    $deplace = move_uploaded_file($image['tmp_name'], $chemin_final);
    
                    if(!$deplace){
                        http_response_code(500);
                        echo json_encode(['error' => 'Impossible de sauvegarder le fichier']);
                        exit;
                    }
    
                    $urlRelative = 'uploads/thumbnails/' . $nomFichier;

                    $result = $manager->updateImage($cours_id, ['url_image' => $urlRelative]);

                    if($result === false){
                        http_response_code(500);
                        echo json_encode(['error' => "Erreur lors de l'enregistrement de l'image"]);
                        exit;
                    }

                    http_response_code(201);
                    echo json_encode(['url' => $urlRelative]);
            }
        }
    } else {
        http_response_code(405);
        echo json_encode(['error' => 'Méthode non autorisée']);
    }

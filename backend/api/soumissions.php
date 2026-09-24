<?php
    require_once(__DIR__ . '/../vendor/autoload.php');
    $dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/..');
    $dotenv->load();

    header('Access-Control-Allow-Origin: ' . $_ENV['FRONTEND_URL']);
    header('Access-Control-Allow-Credentials: true');
    header('Access-Control-Allow-Headers: Content-Type');
    header('Content-Type: application/json');
    header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');

    require_once(__DIR__ . '/../classes/gestion_exercices/ExerciceManager.php');
    require_once(__DIR__ . '/../classes/authentification/Authentification.php');
    
    $manager = new ExerciceManager();
    $auth = new Authentification();

    if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
        http_response_code(200);
        exit;
    }
    
    if($_SERVER['REQUEST_METHOD'] === 'GET'){
        if(isset($_GET['id'])){
            if(isset($_GET['formateur'])){
                if(!$auth->verifierRole('Formateur') ){
                    http_response_code(403);
                    echo json_encode(['error' => 'Accès refusé']);
                    exit;
                }

                $soumissionDashboard = $manager->getSoumissionDashboard($_GET['id']);

                if($soumissionDashboard === false){
                    http_response_code(400);
                    echo json_encode(['error' => 'Impossible de récupérer les soumissions']);
                    exit;
                }

                http_response_code(200);
                echo json_encode($soumissionDashboard);
            } else {
                if(!$auth->verifierRole('Formateur') ){
                    http_response_code(403);
                    echo json_encode(['error' => 'Accès refusé']);
                    exit;
                }

                $soumissionsByFormateur = $manager->getSoumissionFormateur($_GET['id']);

                if($soumissionsByFormateur === false){
                    http_response_code(400);
                    echo json_encode(['error' => 'Impossible de récupérer les soumissions']);
                    exit;
                }

                http_response_code(200);
                echo json_encode($soumissionsByFormateur);
            }
        } else {
            http_response_code(400);
            echo json_encode(['error' => 'Id manquante']);
            exit;
        }
    } else if($_SERVER['REQUEST_METHOD'] === 'PUT'){
         if(!isset($_GET['id'])){
            http_response_code(400);
            echo json_encode(['error' => 'Id manquant']);
            exit;
        } else {
            if(!$auth->verifierRole('Formateur') ){
                http_response_code(403);
                echo json_encode(['error' => 'Accès refusé']);
                exit;
            }

            $data = json_decode(file_get_contents("php://input"), true);

            $user = $auth->verifierSession();
            $formateur_id = $user['utilisateur_id'];
            
            $result = $manager->corrigerSoumission($_GET['id'], $formateur_id, $data['note'], $data['commentaire']);
            
            if($result === false){
                http_response_code(400);
                echo json_encode(['error' => 'Impossible de modifier la soumission']);
                exit;
            }
    
            http_response_code(200);
            echo json_encode($result);
        }
    } else {
        http_response_code(405);
        echo json_encode(['error' => 'Méthode non autorisée']);
    }

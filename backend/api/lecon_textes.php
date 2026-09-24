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
    require_once(__DIR__ . '/../classes/authentification/Authentification.php');

    $manager = new CoursManager();
    $auth = new Authentification();

    if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
        http_response_code(200);
        exit;
    } else if($_SERVER['REQUEST_METHOD'] === 'POST'){
        if(!isset($_GET['id'])){
            http_response_code(400);
            echo json_encode(['error' => 'Id cours manquant']);
            exit;
        } else {
            if(!isset($_GET['leconId'])){
                http_response_code(400);
                echo json_encode(['error' => 'Id leçon manquant']);
                exit;
            } else {
                if(!$auth->verifierRole('Formateur')){
                    http_response_code(403);
                    echo json_encode(['error' => 'Accès refusé']);
                    exit;
                }

                $data = json_decode(file_get_contents("php://input"), true);

                if(!isset($data['contenu'])){
                    http_response_code(400);
                    echo json_encode(['error' => 'Contenu manquant']);
                    exit;
                }

                $success = $manager->createLeconTexte($_GET['leconId'], $_GET['id'], $data['contenu']);

                if($success === false){
                    http_response_code(500);
                    echo json_encode(['error' => 'Erreur serveur']);
                    exit;
                }

                if(is_array($success) && isset($success['error'])){
                    http_response_code(400);
                    echo json_encode($success);
                    exit;
                }

                http_response_code(200);
                echo json_encode($success);
            }
        }
    } else {
        http_response_code(405);
        echo json_encode(['error' => 'Méthode non autorisée']);
    }
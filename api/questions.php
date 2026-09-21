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
    
    $manager = new ExerciceManager();

    if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
        http_response_code(200);
        exit;
    } else if($_SERVER['REQUEST_METHOD'] === 'GET'){
        if(!isset($_GET['id'])){
            http_response_code(400);
            echo json_encode(['error' => 'Id manquante']);
            exit;
        } else {
            if(isset($_GET['allQuestion'])){
                $allQuestions = $manager->getQuestionsByExercice($_GET['id']);

                if($allQuestions === false){
                    http_response_code(500);
                    echo json_encode(['error' => 'Impossible de récupérer les questions']);
                    exit;
                }

                http_response_code(200);
                echo json_encode($allQuestions);
            }
        }
    } else if($_SERVER['REQUEST_METHOD'] === 'POST') {
        $data = json_decode(file_get_contents("php://input"), true);

        $result = $manager->creerQuestion($data);

        if($result === false){
            http_response_code(500);
            echo json_encode(['error' => 'Impossible de crée la question']);
            exit;
        }
        
        http_response_code(201);
        echo json_encode($result);

    } else if($_SERVER['REQUEST_METHOD'] === 'PUT'){
        if(!isset($_GET['id'])){
            http_response_code(400);
            echo json_encode(['error' => 'Id manquante']);
            exit;
        } else {
            $data = json_decode(file_get_contents("php://input"), true);
    
            $result = $manager->updateQuestion($_GET['id'], $data);
    
            if($result === false){
                http_response_code(500);
                echo json_encode(['error' => 'Impossible de modifier la question']);
                exit;
            }
            
            http_response_code(200);
            echo json_encode($result);
        }
    } else if($_SERVER['REQUEST_METHOD'] === 'DELETE'){
        if(!isset($_GET['id'])){
            http_response_code(400);
            echo json_encode(['error' => 'Id manquante']);
            exit;
        } else {
            $result = $manager->supprimerQuestion($_GET['id']);

            if($result === false){
                http_response_code(500);
                echo json_encode(['error' => 'Impossible de supprimer la question']);
                exit;
            }

            http_response_code(200);
            echo json_encode($result);
        }
    } else {
        http_response_code(405);
        echo json_encode(['error' => 'Méthode non autorisée']);
    }
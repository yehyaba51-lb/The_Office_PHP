<?php
    require_once(__DIR__ . '/../vendor/autoload.php');
    $dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/..');
    $dotenv->load();

    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Headers: Content-Type');
    header('Content-Type: application/json');
    header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');


    require_once(__DIR__ . '/../classes/gestion_cours/CoursManager.php');

    $manager = new CoursManager();

    if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
        http_response_code(200);
        exit;
    } else if($_SERVER['REQUEST_METHOD'] === 'GET'){
        if(!isset($_GET['id'])){
            http_response_code(400);
            echo json_encode(['error' => 'Id manquante']);
            exit;
        } else {
            if(isset($_GET['lecon'])){
                if(isset($_GET['one'])){
                    $lecon = $manager->getLecon($_GET['lecon'],$_GET['id']);
    
                    if($lecon === false){
                        http_response_code(404);
                        echo json_encode(['error' => 'Leçon introuvable']);
                        exit;
                    }
    
                    http_response_code(200);
                    echo json_encode($lecon);

                } else if(isset($_GET['allContent'])){
                    $allContent = $manager->getAllContent($_GET['id'], $_GET['lecon']);

                    if($allContent === false){
                        http_response_code(500);
                        echo json_encode(['error' => 'Impossible de récupérer tous contenues']);
                        exit;
                    }

                    http_response_code(200);
                    echo json_encode($allContent);
                }
            } else {
                $allLeconsByCours = $manager->getLeconsByCours($_GET['id']);
    
                if($allLeconsByCours === false){
                    http_response_code(500);
                    echo json_encode(['error' => 'Id manquante']);
                    exit;
                }
    
                http_response_code(200);
                echo json_encode($allLeconsByCours);
            }
        }
    } else {
        http_response_code(405);
        echo json_encode(['error' => 'Méthode non autorisée']);
    }

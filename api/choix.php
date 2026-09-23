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
            if(!isset($_GET['id'])){
                http_response_code(400);
                echo json_encode(['error' => 'Id manquante']);
                exit;
            } else {
                if(isset($_GET['exercice'])){
                    if(!$auth->verifierRole('Formateur')){
                        http_response_code(403);
                        echo json_encode(['error' => 'Accès refusé']);
                        exit;
                    }

                    $choixExercice = $manager->getChoixByExercice($_GET['id']);
    
                    if($choixExercice === false){
                        http_response_code(500);
                        echo json_encode(['error' => 'Impossible de récupérer les choix']);
                        exit;
                    }
    
                    http_response_code(200);
                    echo json_encode($choixExercice);
                } else {
                    if(!$auth->verifierRole('Formateur')){
                        http_response_code(403);
                        echo json_encode(['error' => 'Accès refusé']);
                        exit;
                    }
                    
                    $allChoix = $manager->getChoixByQuestion($_GET['id']);
        
                    if($allChoix === false){
                        http_response_code(500);
                        echo json_encode(['error' => 'Impossible de récupérer les choix']);
                        exit;
                    }
        
                    http_response_code(200);
                    echo json_encode($allChoix);
    
                }
            }
    } else {
        http_response_code(405);
        echo json_encode(['error' => 'Méthode non autorisée']);
    }
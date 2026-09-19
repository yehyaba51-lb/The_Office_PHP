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
        if(isset($_GET['id'])){
            if(isset($_GET['formateur'])){
                $soumissionDashboard = $manager->getSoumissionDashboard($_GET['id']);

                if($soumissionDashboard === false){
                    http_response_code(400);
                    echo json_encode(['error' => 'Impossible de récupérer les soumissions']);
                    exit;
                }

                http_response_code(200);
                echo json_encode($soumissionDashboard);
            } else {
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
            $allSoumissions = $manager->getAllSoumissions();
        }
    } else {
        http_response_code(405);
        echo json_encode(['error' => 'Méthode non autorisée']);
    }

<?php
    require_once(__DIR__ . '/../vendor/autoload.php');
    $dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/..');
    $dotenv->load();

    header('Access-Control-Allow-Origin: *');
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
            $exercice_rows = $manager->getExercicesByCours($_GET['id']);

            if($exercice_rows === false){
                http_response_code(500);
                echo json_encode(['error' => 'Impossible de récupérer les exercices']);
                exit;
            }

            http_response_code(200);
            echo json_encode($exercice_rows);
        }
    } else if($_SERVER['REQUEST_METHOD'] === 'POST') {
        $data = json_decode(file_get_contents("php://input"), true);

        $result = $manager->creerPlaceholder($data['leconId'], $data['coursId'], $data['exercice_titre']);

        if(!$result){
            http_response_code(400);
            echo json_encode(['error' => 'Exercice ajouté invalide']);
            exit;
        }

        http_response_code(201);
        echo json_encode($result);
    } else {
        http_response_code(405);
        echo json_encode(['error' => 'Méthode non autorisée']);
    }

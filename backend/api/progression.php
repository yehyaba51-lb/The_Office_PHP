<?php
    require_once(__DIR__ . '/../vendor/autoload.php');
    $dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/..');
    $dotenv->load();

    header('Access-Control-Allow-Origin: ' . $_ENV['FRONTEND_URL']);
    header('Access-Control-Allow-Credentials: true');
    header('Access-Control-Allow-Headers: Content-Type');
    header('Content-Type: application/json');
    header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');


    require_once(__DIR__ . '/../classes/suivi_notes/ProgressionTracker.php');
    require_once(__DIR__ . '/../classes/authentification/Authentification.php');

    $tracker = new ProgressionTracker();
    $auth = new Authentification();

    if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
        http_response_code(200);
        exit;
    }

    if($_SERVER['REQUEST_METHOD'] === 'GET'){
        if(!isset($_GET['id'])){
            http_response_code(400);
            echo json_encode(['error' => 'Id manquant']);
            exit;
        } else {
            if(!$auth->verifierRole('Etudiant')){
                http_response_code(403);
                echo json_encode(['error' => 'Accès refusé']);
                exit;
            }

            $getStatistics = $tracker->getStatistiquesEtudiant($_GET['id']);

            if($getStatistics === false){
                http_response_code(500);
                echo json_encode(['error' => 'Erreur serveur']);
                exit;
            }

            echo json_encode($getStatistics);
        }


    }
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

    $manager = new CoursManager();

    if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
        http_response_code(200);
        exit;
    } else if($_SERVER['REQUEST_METHOD'] === 'GET'){
         if(isset($_GET['id'])){
            if(isset($_GET['formateur'])){
                $inscriptionPerFormateur = $manager->getInscriptionByFormateur($_GET['id']);

                if($inscriptionPerFormateur === false){
                    http_response_code(500);
                    echo json_encode(['error' => 'Erreur lors de la récupération']);
                    exit;
                }

                http_response_code(200);
                echo json_encode($inscriptionPerFormateur);
            } else {
                $inscriptionPerCours = $manager->getInscriptionByCours($_GET['id']);
    
                if($inscriptionPerCours === false){
                    http_response_code(500);
                    echo json_encode(['error' => 'Impossible de récupérer les cours']);
                    exit;
                }
    
                http_response_code(200);
                echo json_encode($inscriptionPerCours);
            }
        } else {
            $allInscriptions = $manager->getAllInscriptions();
    
            if($allInscriptions === false){
                http_response_code(500);
                echo json_encode(['error' => 'Impossible de récupérer les inscriptions']);
                exit;
            }
    
            http_response_code(200);
            echo json_encode($allInscriptions);
        }
    } else if($_SERVER['REQUEST_METHOD'] === 'POST'){
        $data = json_decode(file_get_contents("php://input"), true);

        $result = $manager->createInscription($data['etudiant_id'], $data['cours_id']);

        if(!$result){
            http_response_code(400);
            echo json_encode(['error' => 'Inscription ajouté invalide']);
            exit;
        }

        http_response_code(201);
        echo json_encode($result);
    }  else if($_SERVER['REQUEST_METHOD'] === 'DELETE'){
        if(!isset($_GET['id'])){
            http_response_code(400);
            echo json_encode(['error' => 'Id manquant']);
            exit;
        } else {
            $delete = $manager->supprimerInscription($_GET['id']);

            if(!$delete){
                http_response_code(400);
                echo json_encode(['error' => "Impossible de supprimer l'inscription"]);
                exit;
            }

            http_response_code(200);
            echo json_encode($delete);
        }
    } else {
        http_response_code(405);
        echo json_encode(['error' => 'Méthode non autorisée']);
    }
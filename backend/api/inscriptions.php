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
    }
    
    if($_SERVER['REQUEST_METHOD'] === 'GET'){
         if(isset($_GET['id'])){
            if(isset($_GET['formateur'])){
                if(!$auth->verifierRole('Formateur')){
                    http_response_code(403);
                    echo json_encode(['error' => 'Accès refusé']);
                    exit;
                }

                $inscriptionPerFormateur = $manager->getInscriptionByFormateur($_GET['id']);

                if($inscriptionPerFormateur === false){
                    http_response_code(500);
                    echo json_encode(['error' => 'Erreur serveur']);
                    exit;
                }

                http_response_code(200);
                echo json_encode($inscriptionPerFormateur);
            } else if(isset($_GET['new'])){
                if(!$auth->verifierRole('Etudiant')){
                    http_response_code(403);
                    echo json_encode(['error' => 'Accès refusé']);
                    exit;
                }

                $newInscriptions = $manager->getNewInscriptions($_GET['id']);

                if($newInscriptions === false){
                    http_response_code(500);
                    echo json_encode(['error' => 'Erreur serveur']);
                    exit;
                }

                http_response_code(200);
                echo json_encode($newInscriptions);
            } else {
                if(!$auth->verifierRole('Administrateur') && !$auth->verifierRole('Formateur') ){
                    http_response_code(403);
                    echo json_encode(['error' => 'Accès refusé']);
                    exit;
                }

                $inscriptionPerCours = $manager->getInscriptionByCours($_GET['id']);
    
                if($inscriptionPerCours === false){
                    http_response_code(500);
                    echo json_encode(['error' => 'Erreur serveur']);
                    exit;
                }
    
                http_response_code(200);
                echo json_encode($inscriptionPerCours);
            }
        } else {
            if(!$auth->verifierRole('Administrateur')){
                http_response_code(403);
                echo json_encode(['error' => 'Accès refusé']);
                exit;
            }

            $allInscriptions = $manager->getAllInscriptions();
    
            if($allInscriptions === false){
                http_response_code(500);
                echo json_encode(['error' => 'Erreur serveur']);
                exit;
            }
    
            http_response_code(200);
            echo json_encode($allInscriptions);
        }
    } else if($_SERVER['REQUEST_METHOD'] === 'POST'){
        if(!$auth->verifierRole('Administrateur')){
            http_response_code(403);
            echo json_encode(['error' => 'Accès refusé']);
            exit;
        }

        $data = json_decode(file_get_contents("php://input"), true);

        $result = $manager->createInscription($data['etudiant_id'], $data['cours_id']);

        if($result === false){
            http_response_code(500);
            echo json_encode(['error' => 'Erreur serveur']);
            exit;
        }

        if(is_array($result) && isset($result['error'])){
            http_response_code(400);
            echo json_encode($result);
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
            if(!$auth->verifierRole('Administrateur')){
                http_response_code(403);
                echo json_encode(['error' => 'Accès refusé']);
                exit;
            }

            $delete = $manager->supprimerInscription($_GET['id']);

            if($delete === false){
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
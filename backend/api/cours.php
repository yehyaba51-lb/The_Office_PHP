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
    } else if($_SERVER['REQUEST_METHOD'] === 'GET'){
        if(isset($_GET['id'])){
            if(isset($_GET['formateur'])){
                if(!$auth->verifierRole('Formateur')){
                    http_response_code(403);
                    echo json_encode(['error' => 'Accès refusé']);
                    exit;
                }

                $statistics = $manager->getStatistiquesFormateur($_GET['id']);

                if($statistics === false){
                    http_response_code(500);
                    echo json_encode(['error' => 'Erreur serveur']);
                    exit;
                }

                http_response_code(200);
                echo json_encode($statistics);
            } else if(isset($_GET['formateurcours'])){
                if(!$auth->verifierRole('Formateur')){
                    http_response_code(403);
                    echo json_encode(['error' => 'Accès refusé']);
                    exit;
                }
                
                $coursByFormateur = $manager->getCoursByFormateur($_GET['id']);

                if($coursByFormateur === false){
                    http_response_code(500);
                    echo json_encode(['error' => 'Impossible de récupérer les cours']);
                    exit;
                }

                http_response_code(200);
                echo json_encode($coursByFormateur);
            } else if(isset($_GET['allCoursEtudiant'])){
                if(!$auth->verifierRole('Etudiant')){
                    http_response_code(403);
                    echo json_encode(['error' => 'Accès refusé']);
                    exit;
                }

                $coursByEtudiant = $manager->getCoursByEtudiant($_GET['id']);

                if($coursByEtudiant === false){
                    http_response_code(500);
                    echo json_encode(['error' => 'Erreur serveur']);
                    exit;
                }

                http_response_code(200);
                echo json_encode($coursByEtudiant);
            } else {
                $coursById = $manager->getCours($_GET['id']);
    
                if($coursById === false){
                    http_response_code(500);
                    echo json_encode(['error' => "Erreur serveur"]);
                    exit;
                }

                if($coursById === null){
                    http_response_code(404);
                    echo json_encode(['error' => 'Cours introuvable']);
                    exit;
                }
    
                http_response_code(200);
                echo json_encode($coursById);
            }
        } else {
            $allCours = $manager->getAllCours();
            if($allCours === false){
                http_response_code(500);
                echo json_encode(['error' => 'Impossible de récupérer les cours']);
                exit;
            }
    
            http_response_code(200);
            echo json_encode($allCours);
        }
    } else if($_SERVER['REQUEST_METHOD'] === 'POST'){
        if(!$auth->verifierRole('Administrateur')){
            http_response_code(403);
            echo json_encode(['error' => 'Accès refusé']);
            exit;
        }
        $data = json_decode(file_get_contents("php://input"), true);

        $result = $manager->creerPlaceholder($data['cours_titre'], $data['formateur_id'], $data['categorie_id']);
        
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
    } else if($_SERVER['REQUEST_METHOD'] === 'PUT'){
        if(!isset($_GET['id'])){
            http_response_code(400);
            echo json_encode(['error' => 'Id manquant']);
            exit;
        } else {
            if(isset($_GET['description'])){
                if(!$auth->verifierRole('Formateur')){
                    http_response_code(403);
                    echo json_encode(['error' => 'Accès refusé']);
                    exit;
                }

                $data = json_decode(file_get_contents("php://input"), true);

                $result = $manager->updateDescription($_GET['id'], $data);

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

                http_response_code(200);
                echo json_encode($result);
            } else {
                if(!$auth->verifierRole('Administrateur')){
                    http_response_code(403);
                    echo json_encode(['error' => 'Accès refusé']);
                    exit;
                }


                $data = json_decode(file_get_contents("php://input"), true);
        
                $result = $manager->updateCoursByAdmin($_GET['id'], $data);
                
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
        
                http_response_code(200);
                echo json_encode($result);
            }
        }
    } else if($_SERVER['REQUEST_METHOD'] === 'DELETE'){
        if(!isset($_GET['id'])){
            http_response_code(400);
            echo json_encode(['error' => "Id manquant"]);
            exit;
        } else {
            if(!$auth->verifierRole('Administrateur')){
                http_response_code(403);
                echo json_encode(['error' => 'Accès refusé']);
                exit;
            }
            
            $delete = $manager->supprimerCours($_GET['id']);

            if($delete === false){
                http_response_code(400);
                echo json_encode(['error' => "Impossible de supprimer le cours"]);
                exit;
            }

            http_response_code(200);
            echo json_encode($delete);
        }
    } else {
        http_response_code(405);
        echo json_encode(['error' => 'Méthode non autorisée']);
    }

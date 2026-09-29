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

                    $session = $auth->verifierSession();

                    if($session === false){
                        http_response_code(500);
                        echo json_encode(['error' => 'Erreur serveur']);
                        exit;
                    }

                    $user_id = $session['utilisateur_id'];
                    $cours = $manager->getExercice($_GET['id']);

                    if ($cours === false) {
                        http_response_code(500);
                        echo json_encode(['error' => 'Erreur serveur']);
                        exit;
                    }

                    $cours_id = $cours['cours_id'];

                    if(!$auth->verifierAccesFormateur($user_id, $cours_id)){
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
                } else if(isset($_GET['allExercices'])){
                    if(!$auth->verifierRole('Etudiant') ){
                        http_response_code(403);
                        echo json_encode(['error' => 'Accès refusé']);
                        exit;
                    }

                    $session = $auth->verifierSession();

                    if($session === false){
                        http_response_code(500);
                        echo json_encode(['error' => 'Erreur serveur']);
                        exit;
                    }

                    $user_id = $session['utilisateur_id'];
                    $cours = $manager->getExercice($_GET['id']);

                    if ($cours === false) {
                        http_response_code(500);
                        echo json_encode(['error' => 'Erreur serveur']);
                        exit;
                    }

                    $cours_id = $cours['cours_id'];

                    if(!$auth->verifierAccesEtudiant($user_id, $cours_id)){
                        http_response_code(403);
                        echo json_encode(['error' => 'Accès refusé']);
                        exit;
                    }

                    $allQuestionsPerExercice = $manager->getChoixPerExercice($_GET['id']);

                    if($allQuestionsPerExercice === false){
                        http_response_code(500);
                        echo json_encode(['error' => 'Erreur serveur']);
                        exit;
                    }

                    http_response_code(200);
                    echo json_encode($allQuestionsPerExercice);
                } else {
                    if(!$auth->verifierRole('Formateur')){
                        http_response_code(403);
                        echo json_encode(['error' => 'Accès refusé']);
                        exit;
                    }

                    $session = $auth->verifierSession();

                    if($session === false){
                        http_response_code(500);
                        echo json_encode(['error' => 'Erreur serveur']);
                        exit;
                    }

                    $user_id = $session['utilisateur_id'];

                    $exercice = $manager->getExerciceIdByQuestion($_GET['id']);

                    if($exercice === false){
                        http_response_code(500);
                        echo json_encode(['error' => 'Erreur serveur']);
                        exit;
                    }

                    $cours = $manager->getExercice($exercice['exercice_id']);

                    if ($cours === false) {
                        http_response_code(500);
                        echo json_encode(['error' => 'Erreur serveur']);
                        exit;
                    }

                    $cours_id = $cours['cours_id'];

                    if(!$auth->verifierAccesFormateur($user_id, $cours_id)){
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
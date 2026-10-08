<?php
    require_once __DIR__ . '/../config/config.php';

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
            echo json_encode(['error' => 'Id manquant']);
            exit;
        } else {
            if(isset($_GET['allExercices'])){
                if(!$auth->verifierRole('Etudiant') ){
                    http_response_code(403);
                    echo json_encode(['error' => 'Accès refusé']);
                    exit;
                }

                $session = $auth->verifierSession();

                if(!$session){
                    http_response_code(403);
                    echo json_encode(['error' => 'Accès refusé']);
                    exit;
                }

                $user_id = $session['utilisateur_id'];
                $cours = $manager->getExercice($_GET['id']);

                if (!$cours) {
                    http_response_code(404);
                        echo json_encode(['error' => 'Aucune question trouvé']);
                        exit;
                }

                $cours_id = $cours['cours_id'];

                if(!$auth->verifierAccesEtudiant($user_id, $cours_id)){
                    http_response_code(403);
                    echo json_encode(['error' => 'Accès refusé']);
                    exit;
                }

                $allQuestionsPerExercice = $manager->getQuestionsByExerciceForEtudiant($_GET['id'], $session['utilisateur_id']);
                if($allQuestionsPerExercice === false){
                    http_response_code(500);
                    echo json_encode(['error' => 'Erreur serveur']);
                    exit;
                }

                if(is_array($allQuestionsPerExercice) && isset($allQuestionsPerExercice['empty'])){
                    http_response_code(404);
                    echo json_encode(['error' => 'Leçon introuvable']);
                    exit;
                }

                if(is_array($allQuestionsPerExercice) && isset($allQuestionsPerExercice['error'])){
                    http_response_code(423);
                    echo json_encode($allQuestionsPerExercice);
                    exit;
                }

                http_response_code(200);
                echo json_encode($allQuestionsPerExercice);
            } else if(isset($_GET['allQuestion'])){
                    if(!$auth->verifierRole('Formateur') ){
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
    
                    $allQuestions = $manager->getQuestionsByExercice($_GET['id']);
    
                    if($allQuestions === false){
                        http_response_code(500);
                        echo json_encode(['error' => 'Erreur serveur']);
                        exit;
                    }
    
                    http_response_code(200);
                    echo json_encode($allQuestions);
                }
            }
    } else if($_SERVER['REQUEST_METHOD'] === 'POST') {
        if(!$auth->verifierRole('Formateur') ){
            http_response_code(403);
            echo json_encode(['error' => 'Accès refusé']);
            exit;
        }

        $data = json_decode(file_get_contents("php://input"), true);

        $session = $auth->verifierSession();

        if($session === false){
            http_response_code(500);
            echo json_encode(['error' => 'Erreur serveur']);
            exit;
        }

        $user_id = $session['utilisateur_id'];

        $cours = $manager->getExercice($data['exercice_id']);
                    
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

        $result = $manager->creerQuestion($data);

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
            if(!$auth->verifierRole('Formateur') ){
                http_response_code(403);
                echo json_encode(['error' => 'Accès refusé']);
                exit;
            }

            $data = json_decode(file_get_contents("php://input"), true);
    
            $session = $auth->verifierSession();

            if($session === false){
                http_response_code(500);
                echo json_encode(['error' => 'Erreur serveur']);
                exit;
            }

            $user_id = $session['utilisateur_id'];

            $cours = $manager->getExercice($data['exercice_id']);
                        
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

            $result = $manager->updateQuestion($_GET['id'], $data);
    
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
    } else if($_SERVER['REQUEST_METHOD'] === 'DELETE'){
        if(!isset($_GET['id'])){
            http_response_code(400);
            echo json_encode(['error' => 'Id manquant']);
            exit;
        } else {
            if(!$auth->verifierRole('Formateur') ){
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
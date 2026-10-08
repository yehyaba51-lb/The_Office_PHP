<?php
    require_once __DIR__ . '/../config/config.php';

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
        if(!isset($_GET['id'])){
            http_response_code(400);
            echo json_encode(['error' => 'Id manquante']);
            exit;
        } else {
            if(isset($_GET['lecon'])){
                if(isset($_GET['one'])){
                    if(!$auth->verifierRole('Administrateur') && !$auth->verifierRole('Formateur') && !$auth->verifierRole('Etudiant')){
                        http_response_code(403);
                        echo json_encode(['error' => 'Accès refusé']);
                        exit;
                    }

                    $session = $auth->verifierSession();
                
                    if($session === false){
                        http_response_code(403);
                        echo json_encode(['error' => 'Accès refusé']);
                        exit;
                    }

                    $user_id = $session['utilisateur_id'];
                    $role = $session['role'];
                    $access = false;

                    if($auth->verifierRole('Etudiant')){
                        $access = $auth->verifierAccesEtudiant($user_id, $_GET['id']);
                    } else if($auth->verifierRole('Formateur')){
                        $access = $auth->verifierAccesFormateur($user_id, $_GET['id']);
                    } else if($auth->verifierRole('Administrateur')){
                        $access = true;
                    }

                    if($access === false){
                        http_response_code(403);
                        echo json_encode(['error' => 'Accès refusé']);
                        exit;
                    }

                    $lecon = $manager->getLecon($_GET['lecon'],$_GET['id']);
    
                    if($lecon === false){
                        http_response_code(404);
                        echo json_encode(['error' => 'Leçon introuvable']);
                        exit;
                    }
    
                    http_response_code(200);
                    echo json_encode($lecon);

                } else if(isset($_GET['userId'])){
                    if(isset($_GET['allContent'])){
                        if(!$auth->verifierRole('Formateur') && !$auth->verifierRole('Etudiant')){
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
                        $verifier = false;

                        if($auth->verifierRole('Etudiant')){
                            $verifier = $auth->verifierAccesEtudiant($user_id, $_GET['id']);
                        } else if($auth->verifierRole('Formateur')){
                            $verifier = $auth->verifierAccesFormateur($user_id, $_GET['id']);
                        }

                        if($verifier === false){
                            http_response_code(403);
                            echo json_encode(['error' => 'Accès refusé']);
                            exit;
                        }
    
                        $allContent = $manager->getAllContent($_GET['id'], $_GET['lecon'], $user_id);
    
                        if($allContent === false){
                            http_response_code(500);
                            echo json_encode(['error' => 'Erreur serveur']);
                            exit;
                        }

                        if(is_array($allContent) && isset($allContent['empty'])){
                            http_response_code(404);
                            echo json_encode(['error' => 'Leçon introuvable']);
                            exit;
                        }

                        if(is_array($allContent) && isset($allContent['error'])){
                            http_response_code(423);
                            echo json_encode($allContent);
                            exit;
                        }
    
                        http_response_code(200);
                        echo json_encode($allContent);
                    }
                } else {
                    http_response_code(400);
                    echo json_encode(['error' => 'Paramètre one ou allContent requis']);
                    exit;
                }
            } else {
                if(!$auth->verifierRole('Administrateur') && !$auth->verifierRole('Formateur') ){
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
                $verifier = false;

                if($auth->verifierRole('Formateur')){
                    $verifier = $auth->verifierAccesFormateur($user_id, $_GET['id']);
                } else if($auth->verifierRole('Administrateur')){
                    $verifier = true;
                }

                if($verifier === false){
                    http_response_code(403);
                    echo json_encode(['error' => 'Accès refusé']);
                    exit;
                }

                $allLeconsByCours = $manager->getLeconsByCours($_GET['id']);
    
                if($allLeconsByCours === false){
                    http_response_code(500);
                    echo json_encode(['error' => 'Erreur serveur']);
                    exit;
                }
    
                http_response_code(200);
                echo json_encode($allLeconsByCours);
            }
        }
    } else if($_SERVER['REQUEST_METHOD'] === 'POST'){
        if(!isset($_GET['id'])){
            http_response_code(400);
            echo json_encode(['error' => 'Id cours manquant']);
            exit;
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

            if(!$auth->verifierAccesFormateur($user_id, $_GET['id'])){
                http_response_code(403);
                echo json_encode(['error' => 'Accès refusé']);
                exit;
            }
            
            $data = json_decode(file_get_contents("php://input"), true);
    
            $id = $manager->ajouterLecon($_GET['id'], $data['titre'], $data['ordre']);

            if($id === false){
                http_response_code(500);
                echo json_encode(['error' => 'Erreur serveur']);
                exit;
            }

            if(is_array($id) && isset($id['error'])){
                http_response_code(400);
                echo json_encode($id);
                exit;
            }

            http_response_code(201);
            echo json_encode($id);
        }
    } else {
        http_response_code(405);
        echo json_encode(['error' => 'Méthode non autorisée']);
    }

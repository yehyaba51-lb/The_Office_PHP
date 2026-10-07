<?php
    require_once __DIR__ . '/../config/config.php';

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
    } else if($_SERVER['REQUEST_METHOD'] === 'GET'){
        if(!isset($_GET['id'])){
            http_response_code(400);
            echo json_encode(['error' => 'Id manquant']);
            exit;
        } else {
            if(!isset($_GET['cours'])){
                if(!$auth->verifierRole('Etudiant')){
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
                    
                $etudiant_id = $session['utilisateur_id'];

                $getStatistics = $tracker->getStatistiquesEtudiant($etudiant_id);

                if($getStatistics === false){
                    http_response_code(500);
                    echo json_encode(['error' => 'Erreur serveur']);
                    exit;
                }
                
                http_response_code(200);
                echo json_encode($getStatistics);
            } else {
                if(!$auth->verifierRole('Etudiant')){
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
                    
                $etudiant_id = $session['utilisateur_id'];

                $getCoursTermine = $tracker->getCoursTermine($etudiant_id);

                if($getCoursTermine === false){
                    http_response_code(500);
                    echo json_encode(['error' => 'Erreur serveur']);
                    exit;
                }

                http_response_code(200);
                echo json_encode($getCoursTermine);
            }
        }
    } else if($_SERVER['REQUEST_METHOD'] === 'POST'){
        if(!isset($_GET['id'])){
            http_response_code(400);
            echo json_encode(['error' => 'Id cours manquant']);
            exit;
        } else {
            if(!isset($_GET['leconId'])){
                http_response_code(400);
                echo json_encode(['error' => 'Id leçon manquant']);
                exit;
            } else {
                if(!isset($_GET['etudiantId'])){
                    http_response_code(400);
                    echo json_encode(['error' => 'Id etudinat manquant']);
                    exit;
                } else {
                    if(isset($_GET['nextLecon'])){
                        if(!$auth->verifierRole('Etudiant')){
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
                            
                        $etudiant_id = $session['utilisateur_id'];
    
                        $unlockNextLecon = $tracker->unlockNextLecon($_GET['id'] ,$_GET['leconId'], $etudiant_id);

                        if($unlockNextLecon === false){
                            http_response_code(500);
                            echo json_encode(['error' => 'Erreur serveur']);
                            exit;
                            
                        }

                        if(is_array($unlockNextLecon) && isset($unlockNextLecon['error'])){
                            http_response_code(400);
                            echo json_encode($unlockNextLecon);
                            exit;
                        }

                        http_response_code(201);
                        echo json_encode($unlockNextLecon);
                    } else {
                        http_response_code(400);
                        echo json_encode(['error' => 'Aucune méthode']);
                    }
                }
            }
        }
    } else {
        http_response_code(405);
        echo json_encode(['error' => 'Méthode non autorisée']);
    }
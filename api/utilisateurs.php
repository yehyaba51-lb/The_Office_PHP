<?php
    require_once(__DIR__ . '/../vendor/autoload.php');
    $dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/..');
    $dotenv->load();

    header('Access-Control-Allow-Origin: ' . $_ENV['FRONTEND_URL']);
    header('Access-Control-Allow-Credentials: true');
    header('Access-Control-Allow-Headers: Content-Type');
    header('Content-Type: application/json');
    header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');

    require_once(__DIR__ . '/../classes/authentification/UtilisateurManager.php');
    require_once(__DIR__ . '/../classes/authentification/Authentification.php');
    
    $auth = new Authentification();
    $manager = new UtilisateurManager();

    if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
        http_response_code(200);
        exit;
    } else if($_SERVER['REQUEST_METHOD'] === 'GET'){
        if(isset($_GET['formateur'])){
            if(!$auth->verifierRole('Administrateur') ){
                http_response_code(403);
                echo json_encode(['error' => 'Accès refusé']);
                exit;
            }

            $allFormateur = $manager->getFormateurs();

            if($allFormateur === false){
                http_response_code(500);
                echo json_encode(['error' => 'Impossible de récupérer les formateur']);
                exit;
            }

            http_response_code(200);
            echo json_encode($allFormateur);
        } else {
            if(!$auth->verifierRole('Administrateur') ){
                http_response_code(403);
                echo json_encode(['error' => 'Accès refusé']);
                exit;
            }

            $allUtilisateurs = $manager->getAllUtilisateurs();
    
            if($allUtilisateurs === false){
                http_response_code(500);
                echo json_encode(['error' => 'Impossible de récupérer les utilisateurs']);
                exit;
            }
    
            $data = [];
    
            foreach ($allUtilisateurs as $utilisateur) {
                $data[] = [
                    'id' => $utilisateur->getUtilisateurId(),
                    'prenom' => $utilisateur->getPrenom(),
                    'nom' => $utilisateur->getNom(),
                    'email' => $utilisateur->getEmail(),
                    'role' => $utilisateur->getRole(),
                    'cree_le' => $utilisateur->getCreeLe(),
                ];
            }
            http_response_code(200);
            echo json_encode($data);
        }
    } else if($_SERVER['REQUEST_METHOD'] === 'POST') {
        if(!$auth->verifierRole('Administrateur') ){
            http_response_code(403);
            echo json_encode(['error' => 'Accès refusé']);
            exit;
        }

        $data = json_decode(file_get_contents('php://input'), true);
        
        $result = $manager->creerUtilisateur($data);

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
    } else if($_SERVER['REQUEST_METHOD'] === 'PUT') {
        if(!isset($_GET['id'])){
            http_response_code(400);
            echo json_encode(['error' => 'Id manquant']);
            exit;
        }

        if(isset($_GET['action']) && $_GET['action'] === 'reset'){
            if(!$auth->verifierRole('Administrateur') ){
                http_response_code(403);
                echo json_encode(['error' => 'Accès refusé']);
                exit;
            }

            $result = $manager->reinitialiserMotDePasse($_GET['id']);

            if($result === false){
                http_response_code(500);
                echo json_encode(['error' => 'Erreur serveur']);
                exit;
            }

            http_response_code(200);
            echo json_encode($result);
        } else {
            if(!$auth->verifierRole('Administrateur') ){
                http_response_code(403);
                echo json_encode(['error' => 'Accès refusé']);
                exit;
            }

            $data = json_decode(file_get_contents("php://input"), true);
            
            $update = $manager->updateUtilisateur($_GET['id'], $data);

            if($update === false){
                http_response_code(500);
                echo json_encode(['error' => 'Erreur serveur']);
                exit;
            }

            if(is_array($update) && isset($update['error'])){
                http_response_code(400);
                echo json_encode($update);
                exit;
            }
            
            http_response_code(200);
            echo json_encode($update);
        }
    } else if($_SERVER['REQUEST_METHOD'] === 'DELETE') {
        if(!isset($_GET['id'])){
            http_response_code(400);
            echo json_encode(['error' => 'Id manquant']);
            exit;
        } else {
            if(!$auth->verifierRole('Administrateur') ){
                http_response_code(403);
                echo json_encode(['error' => 'Accès refusé']);
                exit;
            }
            
            $delete = $manager->supprimerUtilisateur($_GET['id']);

            if($delete === false){
                http_response_code(400);
                echo json_encode(['error' => "Impossible de supprimer l'utilisateur"]);
                exit;
            }

            http_response_code(200);
            echo json_encode($delete);
        }
    } else {
        http_response_code(405);
        echo json_encode(['error' => 'Méthode non autorisée']);
    }
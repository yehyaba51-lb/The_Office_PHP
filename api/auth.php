<?php
    require_once(__DIR__ . '/../vendor/autoload.php');
    $dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/..');
    $dotenv->load();

    header('Access-Control-Allow-Origin: ' . $_ENV['FRONTEND_URL']);
    header('Access-Control-Allow-Credentials: true');
    header('Access-Control-Allow-Headers: Content-Type');
    header('Content-Type: application/json');
    header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');


    require_once(__DIR__ . '/../classes/authentification/Authentification.php');
    require_once(__DIR__ . '/../classes/acces_donnees/UtilisateurModel.php');

    $manager = new Authentification();

    require_once(__DIR__ . '/../classes/acces_donnees/BaseDeDonnee.php');
    require_once(__DIR__ . '/../classes/acces_donnees/UtilisateurModel.php');

    $db = new BaseDeDonnee();
    $utilisateurModel = new UtilisateurModel($db);

    if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
        http_response_code(200);
        exit;
    } else if($_SERVER['REQUEST_METHOD'] === 'GET'){
        $row = $manager->verifierSession();

        if(!$row){
            http_response_code(401);
            echo json_encode(['error' => 'Pas de session']);
            exit;
        }

        http_response_code(200);
        echo json_encode($row);
    } else if($_SERVER['REQUEST_METHOD'] === 'POST'){
        $data = json_decode(file_get_contents("php://input"), true);

        $user = $manager->connecter($data['email'], $data['mot_de_passe']);

        if(!$user){
            http_response_code(401);
            echo json_encode(['error' => 'Email ou mot de passe incorrect']);
            exit;
        }

        unset($user['mot_de_passe']);

        http_response_code(200);
        echo json_encode($user);
    } else if($_SERVER['REQUEST_METHOD'] === 'PUT'){
        if($_GET['action'] === 'passer'){
            $session = $manager->verifierSession();

            if(!$session){
                http_response_code(401);
                echo json_encode(['error' => 'Pas de session']);
                exit;
            }
            
            $result = $utilisateurModel->changerPremiereConnexion($session['utilisateur_id']);

            if(!$result){
                http_response_code(400);
                echo json_encode(['error' => 'Impossible de passer mot de passe']);
                exit;
            }

            http_response_code(200);
            echo json_encode(['role' => $session['role']]);
        } else if($_GET['action'] === 'changer'){
            $data = json_decode(file_get_contents("php://input"), true);
            $session = $manager->verifierSession();

            if(!$session){
                http_response_code(401);
                echo json_encode(['error' => 'Pas de session']);
                exit;
            }

            $result = $utilisateurModel->changerPassword($session['utilisateur_id'], $data);

            if(!$result){
                http_response_code(400);
                echo json_encode(['error' => 'Impossible de changer mot de passe']);
                exit;
            }

            http_response_code(200);
            echo json_encode(['role' => $session['role']]);
        }
    } else if($_SERVER['REQUEST_METHOD'] === 'DELETE'){
        $disconnect = $manager->deconnecter();

        if(!$disconnect){
            http_response_code(400);
           echo json_encode(['error' => 'Impossible de se déconnecter']);
            exit;
        }

        http_response_code(200);
        echo json_encode(['success' => 'true']);
    } else {
        http_response_code(405);
        echo json_encode(['error' => 'Méthode non autorisée']);
    }
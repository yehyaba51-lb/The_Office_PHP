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
    require_once(__DIR__ . '/../classes/support/FichierValidateur.php');
    
    $manager = new ExerciceManager();
    $auth = new Authentification();
    $validateur = new FichierValidateur();

    if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
        http_response_code(200);
        exit;
    }
    
    if($_SERVER['REQUEST_METHOD'] === 'GET'){
        if(isset($_GET['id'])){
            if(isset($_GET['formateur'])){
                if(!$auth->verifierRole('Formateur') ){
                    http_response_code(403);
                    echo json_encode(['error' => 'Accès refusé']);
                    exit;
                }

                $soumissionDashboard = $manager->getSoumissionDashboard($_GET['id']);

                if($soumissionDashboard === false){
                    http_response_code(400);
                    echo json_encode(['error' => 'Impossible de récupérer les soumissions']);
                    exit;
                }

                http_response_code(200);
                echo json_encode($soumissionDashboard);
            } else if(isset($_GET['corrige'])){
                if(!$auth->verifierRole('Etudiant') ){
                    http_response_code(403);
                    echo json_encode(['error' => 'Accès refusé']);
                    exit;
                }

                $SoumissionsCorrige = $manager->getSoumissionsCorrige($_GET['id']);

                if($SoumissionsCorrige === false){
                    http_response_code(500);
                    echo json_encode(['error' => 'Erreur serveur']);
                    exit;
                }

                http_response_code(200);
                echo json_encode($SoumissionsCorrige);
            } else if(isset($_GET['notes'])){
                if(!$auth->verifierRole('Etudiant') ){
                    http_response_code(403);
                    echo json_encode(['error' => 'Accès refusé']);
                    exit;
                }

                $etudiantNotes = $manager->getNotes($_GET['id']);

                if($etudiantNotes === false){
                    http_response_code(500);
                    echo json_encode(['error' => 'Erreur serveur']);
                    exit;
                }

                http_response_code(200);
                echo json_encode($etudiantNotes);
            } else if(isset($_GET['singleSoumission'])){
                if(!$auth->verifierRole('Etudiant') ){
                    http_response_code(403);
                    echo json_encode(['error' => 'Accès refusé']);
                    exit;
                }

                $singleSoumission = $manager->getSoumission($_GET['id']);

                if($singleSoumission === false){
                    http_response_code(500);
                    echo json_encode(['error' => 'Erreur serveur']);
                    exit;
                }

                http_response_code(200);
                echo json_encode($singleSoumission);
            } else if(isset($_GET['exerciceId'])){
                if(isset($_GET['getIds'])){
                    if(!$auth->verifierRole('Etudiant') ){
                        http_response_code(403);
                        echo json_encode(['error' => 'Accès refusé']);
                        exit;
                    } else {

                    $getIds = $manager->getDoneSoumissionsIds($_GET['id'], $_GET['exerciceId']);

                    if($getIds === false){
                        http_response_code(500);
                        echo json_encode(['error' => 'Erreur serveur']);
                        exit;
                    }

                    http_response_code(200);
                    echo json_encode($getIds);
                    }
                }
            } else {
                if(!$auth->verifierRole('Formateur') ){
                    http_response_code(403);
                    echo json_encode(['error' => 'Accès refusé']);
                    exit;
                }

                $soumissionsByFormateur = $manager->getSoumissionFormateur($_GET['id']);

                if($soumissionsByFormateur === false){
                    http_response_code(400);
                    echo json_encode(['error' => 'Impossible de récupérer les soumissions']);
                    exit;
                }

                http_response_code(200);
                echo json_encode($soumissionsByFormateur);
            }
        } else {
            http_response_code(400);
            echo json_encode(['error' => 'Id manquante']);
            exit;
        }
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

            $user = $auth->verifierSession();
            $formateur_id = $user['utilisateur_id'];
            
            $result = $manager->corrigerSoumission($_GET['id'], $formateur_id, $data['note'], $data['commentaire']);
            
            if($result === false){
                http_response_code(400);
                echo json_encode(['error' => 'Impossible de modifier la soumission']);
                exit;
            }
    
            http_response_code(200);
            echo json_encode($result);
        }
    } else if($_SERVER['REQUEST_METHOD'] === 'POST'){
        if(isset($_GET['file'])){
            if(!$auth->verifierRole('Etudiant') ){
                http_response_code(403);
                echo json_encode(['error' => 'Accès refusé']);
                exit;
            }

            $file = $_FILES['file'];

            $allowedTypes = [
                "application/pdf", 
                "application/vnd.openxmlformats-officedocument.wordprocessingml.document", // .docx
                "application/msword"
                ];

            $valide = $validateur->valider($file, $allowedTypes, 20 * 1024 * 1024);

            if(is_array($valide) && isset($valide['error'])){
                http_response_code(400);
                echo json_encode($valide);
                exit;
            }

            $dossierUploads = __DIR__ . '/../../uploads/soumissions/';

            if(!is_dir($dossierUploads)){
                mkdir($dossierUploads, 0777, true);
            }

            $nomFichier  = $validateur->genererNomFichier($file['name'], 'soumission', $dossierUploads);

            $chemin_final = $dossierUploads . '/' . $nomFichier;

            $deplace = move_uploaded_file($file['tmp_name'], $chemin_final);

            if(!$deplace){
                http_response_code(500);
                echo json_encode(['error' => "Erreur lors de l'enregistrement du fichier"]);
                exit;
            }

            $urlRelative = 'uploads/soumissions/' . $nomFichier;

            $result = $manager->creerSoumissionFile($_POST['etudiant_id'], $_POST['question_id'], $urlRelative);

            if($result === false){
                http_response_code(500);
                echo json_encode(['error' => "Erreur lors de l'enregistrement du fichier"]);
                exit;
            }

            http_response_code(201);
            echo json_encode(['url' => $urlRelative]);

        } else if(isset($_GET['text'])){
            if(!$auth->verifierRole('Etudiant') ){
                http_response_code(403);
                echo json_encode(['error' => 'Accès refusé']);
                exit;
            }

            $data = json_decode(file_get_contents("php://input"), true);
            if(!isset($data['etudiant_id'])){
                http_response_code(400);
                echo json_encode(['error' => 'Id etudiant manquant']);
                exit;
            } else {
                if(!isset($data['question_id'])){
                    http_response_code(400);
                    echo json_encode(['error' => 'Id question manquant']);
                    exit;
                } else {
                    $submit = $manager->creerSoumissionText($data['etudiant_id'], $data['question_id'], $data['soumission']);

                    if($submit === false){
                        http_response_code(500);
                        echo json_encode(['error' => 'Erreur serveur']);
                        exit;
                    }

                    if(is_array($submit) && isset($submit['error'])){
                        http_response_code(400);
                        echo json_encode($submit);
                        exit;
                    }

                    http_response_code(200);
                    echo json_encode($submit);
                }
            } 
        } else {
            http_response_code(405);
            echo json_encode(['error' => 'Méthode non autorisée']);
        }
        
    } else {
        http_response_code(405);
        echo json_encode(['error' => 'Méthode non autorisée']);
    }

<?php
    require_once __DIR__ . '/../config/config.php';

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

                $session = $auth->verifierSession();

                if($session === false){
                    http_response_code(403);
                    echo json_encode(['error' => 'Accès refusé']);
                    exit;
                }
                    
                $formateur_id = $session['utilisateur_id'];

                $soumissionDashboard = $manager->getSoumissionDashboard($formateur_id);

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

                $session = $auth->verifierSession();

                if($session === false){
                    http_response_code(403);
                    echo json_encode(['error' => 'Accès refusé']);
                    exit;
                }
                    
                $etudiant_id = $session['utilisateur_id'];

                $SoumissionsCorrige = $manager->getSoumissionsCorrige($etudiant_id);

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

                $session = $auth->verifierSession();

                if($session === false){
                    http_response_code(403);
                    echo json_encode(['error' => 'Accès refusé']);
                    exit;
                }
                    
                $etudiant_id = $session['utilisateur_id'];

                $etudiantNotes = $manager->getNotes($etudiant_id);

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

                $session = $auth->verifierSession();

               if($session === false){
                    http_response_code(403);
                    echo json_encode(['error' => 'Accès refusé']);
                    exit;
                }

                $etudiant_id = $session['utilisateur_id'];
                
                $singleSoumission = $manager->getSoumission($_GET['id']);

                if(!$singleSoumission){
                    http_response_code(404);
                    echo json_encode(['error' => 'Aucune soumission trouvé']);
                    exit;
                }

                if($singleSoumission['etudiant_id'] !== $etudiant_id){
                    http_response_code(403);
                    echo json_encode(['error' => 'Accès refusé']);
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

                    $session = $auth->verifierSession();

                    if(!$session){
                        http_response_code(403);
                        echo json_encode(['error' => 'Accès refusé']);
                        exit;
                    }
                            
                    $etudiant_id = $session['utilisateur_id'];

                    $cours = $manager->getExercice($_GET['exerciceId']);

                    if (!$cours) {
                        http_response_code(404);
                        echo json_encode(['error' => 'Aucun cours trouvé']);
                        exit;
                    }

                    $cours_id = $cours['cours_id'];

                    if(!$auth->verifierAccesEtudiant($etudiant_id, $cours_id)){
                        http_response_code(403);
                        echo json_encode(['error' => 'Accès refusé']);
                        exit;
                    }

        
                    $getIds = $manager->getDoneSoumissionsIds($etudiant_id, $_GET['exerciceId']);

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

                $session = $auth->verifierSession();

                if($session === false){
                    http_response_code(403);
                    echo json_encode(['error' => 'Accès refusé']);
                    exit;
                }
                    
                $formateur_id = $session['utilisateur_id'];

                $soumissionsByFormateur = $manager->getSoumissionFormateur($formateur_id);

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

            $session = $auth->verifierSession();

            if($session === false){
                http_response_code(403);
                echo json_encode(['error' => 'Accès refusé']);
                exit;
            }
                    
            $formateur_id = $session['utilisateur_id'];

            $soumission = $manager->getSoumission($_GET['id']);

            if($soumission === false){
                http_response_code(500);
                echo json_encode(['error' => 'Erreur serveur']);
                exit;
            }

            $question_id = $soumission['question_id'];

            $exercice = $manager->getExerciceIdByQuestion($question_id);

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

            if(!$auth->verifierAccesFormateur($formateur_id, $cours_id)){
                http_response_code(403);
                echo json_encode(['error' => 'Accès refusé']);
                exit;
            }
            
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
            if(!isset($_GET['questionId'])){
                http_response_code(400);
                echo json_encode(['error' => 'Id manquant']);
                exit;
            }

            if(!$auth->verifierRole('Etudiant') ){
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

            $exercice = $manager->getExerciceIdByQuestion($_GET['questionId']);

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

            if(!$auth->verifierAccesEtudiant($etudiant_id, $cours_id)){
                http_response_code(403);
                echo json_encode(['error' => 'Accès refusé']);
                exit;
            }

            

            if(!isset($_FILES['file'])){
                http_response_code(400);
                echo json_encode(['error' => 'Aucun fichier envoyé']);
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

            $result = $manager->creerSoumissionFile($etudiant_id, $_GET['questionId'], $urlRelative);

            if($result === false){
                http_response_code(500);
                echo json_encode(['error' => "Erreur lors de l'enregistrement du fichier"]);
                exit;
            }

            http_response_code(201);
            echo json_encode(['url' => $urlRelative]);

        } else if(isset($_GET['text'])){
            if(!isset($_GET['questionId'])){
                http_response_code(400);
                echo json_encode(['error' => 'Id manquant']);
                exit;
            }

            if(!$auth->verifierRole('Etudiant') ){
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

            $exercice = $manager->getExerciceIdByQuestion($_GET['questionId']);

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

            if(!$auth->verifierAccesEtudiant($etudiant_id, $cours_id)){
                http_response_code(403);
                echo json_encode(['error' => 'Accès refusé']);
                exit;
            }

            $data = json_decode(file_get_contents("php://input"), true);

            $submit = $manager->creerSoumissionText($etudiant_id, $_GET['questionId'], $data['soumission']);

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
            
        } else {
            http_response_code(405);
            echo json_encode(['error' => 'Méthode non autorisée']);
        }
        
    } else {
        http_response_code(405);
        echo json_encode(['error' => 'Méthode non autorisée']);
    }

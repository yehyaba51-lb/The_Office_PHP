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
    require_once(__DIR__ . '/../classes/support/FichierValidateur.php');
    require_once(__DIR__ . '/../classes/authentification/Authentification.php');

    $manager = new CoursManager();
    $validateur = new FichierValidateur();
    $auth = new Authentification();

    if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
        http_response_code(200);
        exit;
    } else if($_SERVER['REQUEST_METHOD'] === 'POST'){
        if(!isset($_GET['id'])){
            http_response_code(400);
            echo json_encode(['error' => 'Id cours manquante']);
            exit;
        } else {
            if(!isset($_GET['leconId'])){
                http_response_code(400);
                echo json_encode(['error' => 'Id leçon manquante']);
                exit;
            } else {
                if(!isset($_FILES['video'])){
                    http_response_code(400);
                    echo json_encode(['error' => 'video pas envoyé']);
                    exit;
                } else {
                    if(!$auth->verifierRole('Formateur')){
                        http_response_code(403);
                        echo json_encode(['error' => 'Accès refusé']);
                        exit;
                    }
                    
                    $video = $_FILES['video'];
                    $video_ordre = $_POST['video_ordre'];
                    $video_duree = $_POST['duree'];

                    if(empty($video)){
                        http_response_code(400);
                        echo json_encode(['error' => "Pas de fichier video uploadé"]);
                        exit;
                    } else if(empty($video_ordre)){
                        http_response_code(400);
                        echo json_encode(['error' => "Pas de video ordre"]);
                        exit;
                    } else if(empty($video_duree)){
                        http_response_code(400);
                        echo json_encode(['error' => "Pas de video duree"]);
                        exit;
                    } else {
                        $valide = $validateur->valider($video, ['video/mp4', 'video/quicktime', 'video/x-m4v'], 500 * 1024 * 1024);


                        if($valide === false){
                            http_response_code(400);
                            echo json_encode([
                                'error' => 'Fichier invalide',
                                'mime' => $validateur->getMimeType($video['tmp_name'])
                            ]);
                            exit;
                        }

                        $dossierUploads = __DIR__ . '/../../uploads/videos/';

                        if(!is_dir($dossierUploads)){
                            mkdir($dossierUploads, 0777, true);
                        }

                        $nomFichier  = $validateur->genererNomFichier($video['name'], 'video', $dossierUploads);

                        $chemin_final = $dossierUploads . '/' . $nomFichier;

                        $deplace = move_uploaded_file($video['tmp_name'], $chemin_final);

                        if(!$deplace){
                            http_response_code(500);
                            echo json_encode(['error' => 'Impossible de sauvegarder le fichier']);
                            exit;
                        }

                        $urlRelative = 'uploads/videos/' . $nomFichier;

                        $result = $manager->createLeconVideo($_GET['leconId'], $_GET['id'], $urlRelative, $video_ordre, $video_duree);

                        if($result === false){
                            http_response_code(500);
                            echo json_encode(['error' => "Error uploadé video"]);
                            exit;
                        }

                        http_response_code(201);
                        echo json_encode(['url' => $urlRelative]);
                    }
                }
            }
        }
    } else {
        http_response_code(405);
        echo json_encode(['error' => 'Méthode non autorisée']);
    }
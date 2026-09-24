<?php
require_once(__DIR__ . '/../acces_donnees/BaseDeDonnee.php');
require_once(__DIR__ . '/../acces_donnees/CoursModel.php');
require_once(__DIR__ . '/../acces_donnees/LeconModel.php');
require_once(__DIR__ . '/../acces_donnees/LeconTexteModel.php');
require_once(__DIR__ . '/../acces_donnees/LeconPdfModel.php');
require_once(__DIR__ . '/../acces_donnees/LeconVideoModel.php');
require_once(__DIR__ . '/../acces_donnees/InscriptionModel.php');
require_once(__DIR__ . '/../acces_donnees/CategorieModel.php');
require_once(__DIR__ .'/../acces_donnees/SoumissionModel.php');
require_once(__DIR__ . '/../acces_donnees/ProgressionModel.php');
require_once('Cours.php');
require_once('Lecon.php');
require_once('LeconTexte.php');
require_once('LeconPdf.php');
require_once('LeconVideo.php');
require_once(__DIR__ . '/../authentification/Etudiant.php');
require_once('Inscription.php');
require_once('Categorie.php');

class CoursManager
{
    private $coursModel;
    private $leconModel;
    private $leconTexteModel;
    private $leconPdfModel;
    private $leconVideoModel;
    private $inscriptionModel;
    private $categorieModel;
    private $soumissionModel;
    private $progressionModel;

    public function __construct()
    {
        $db = new BaseDeDonnee();
        $this->coursModel = new CoursModel($db);
        $this->leconModel = new LeconModel($db);
        $this->leconTexteModel = new LeconTexteModel($db);
        $this->leconPdfModel = new LeconPdfModel($db);
        $this->leconVideoModel = new LeconVideoModel($db);
        $this->inscriptionModel = new InscriptionModel($db);
        $this->categorieModel = new CategorieModel($db);
        $this->soumissionModel = new SoumissionModel($db);
        $this->progressionModel = new ProgressionModel($db);
    }

    public function creerPlaceholder($cours_titre, $formateur_id, $categorie_id)
    {
        $data = [
            'cours_titre' => $cours_titre,
            'formateur_id' => $formateur_id,
            'categorie_id' => $categorie_id,
        ];

        return $this->coursModel->creerCours($data);
    }

    public function updateDescription($cours_id, $data){
        return $this->coursModel->updateDescription($cours_id, $data);
    }

    public function ajouterLecon($cours_id, $lecon_titre, $lecon_ordre)
    {
        $data = [
            'cours_id' => $cours_id,
            'lecon_titre' => $lecon_titre,
            'lecon_ordre' => $lecon_ordre
        ];

        return $this->leconModel->creerLecon($data);
    }

    public function getCours($id)
    {
        $row = $this->coursModel->getCours($id);

        if ($row === false) {
            return false;
        }

        return $row;
    }


    public function getAllCours()
    {
        $rows = $this->coursModel->getAllCours();
            
        if($rows === false){
            return false;
        }

        return $rows;
    }


    public function getCoursByFormateur($formateur_id)
    {
        $rows = $this->coursModel->getCoursByFormateur($formateur_id);
        
        if($rows === false){
            return false;
        }

        return $rows;
    }


    public function getStatistiquesFormateur($formateur_id){
        $cours = $this->coursModel->getNombreCours($formateur_id);
        if($cours === false){
            return false;
        }
        $inscriptions = $this->inscriptionModel->compterEtudiants($formateur_id);
        if($inscriptions === false){
            return false;
        }
        $soumissions = $this->soumissionModel->compterSoumissionsNonCorrigees($formateur_id);
        if($soumissions === false) {
            return false;
        }
        $completion = $this->progressionModel->calculerTauxCompletion($formateur_id);
        if($completion === false){ 
            return false;
        }
        return [
            'cours' => $cours,
            'etudiants' => $inscriptions,
            'soumissions' => $soumissions,
            'completion' => $completion
        ];
    }

    public function getLecon($lecon_id, $cours_id){
        $row = $this->leconModel->getLecon($lecon_id, $cours_id);

        if($row === false){
            return false;
        }

        return $row;
    }

    public function getLeconsByCours($cours_id)
    {
        $rows = $this->leconModel->getLeconsByCours($cours_id);
        $result = [];

        if($rows === false){
            return false;
        }

        foreach ($rows as $row) {
            $types = [];
            if($row['has_video']) $types[] = 'Vidéo';
            if($row['has_pdf']) $types[] = 'PDF';
            if($row['has_texte']) $types[] = 'Texte';

            $row['types'] = $types;
            $result[] = $row;
        }
        return $result;
    }

    public function getAllContent($cours_id, $lecon_id){
        $videos = $this->leconVideoModel->getLeconVideosByLecon($cours_id, $lecon_id);
        if($videos === false){
            return false;
        }

        $textes = $this->leconTexteModel->getLeconTextesByLecon($cours_id, $lecon_id);
        if($textes === false){
            return false;
        }

        $pdfs = $this->leconPdfModel->getLeconPdfsByLecon($cours_id, $lecon_id);
        if($pdfs === false){
            return false;
        }

        $allContent = [
            'videos' => $videos,
            'textes' => $textes,
            'pdfs' => $pdfs
        ];

        if($allContent === false){
            return false;
        }

        return $allContent;
    }

    public function createLeconTexte($lecon_id, $cours_id, $contenu_texte)
    {
        $data = [
            'lecon_id' => $lecon_id,
            'cours_id' => $cours_id,
            'contenu_texte' => $contenu_texte
        ];

        return $this->leconTexteModel->creerLeconTexte($data);
    }


    public function createLeconPdf($lecon_id, $cours_id, $url_pdf, $pdf_order)
    {
        $data = [
            'lecon_id' => $lecon_id,
            'cours_id' => $cours_id,
            'url_pdf' => $url_pdf,
            'pdf_order' => $pdf_order
        ];

        return $this->leconPdfModel->creerLeconPdf($data);
    }

    public function updateImage($cours_id, $data){
        return $this->coursModel->updateImage($cours_id, $data);
}

    
    public function createLeconVideo($lecon_id, $cours_id, $url_video, $video_order, $duree)
    {
        $data = [
            'lecon_id' => $lecon_id,
            'cours_id' => $cours_id,
            'url_video' => $url_video,
            'video_order' => $video_order,
            'duree' => $duree,
        ];

        return $this->leconVideoModel->creerLeconVideo($data);
    }


    public function getCoursByEtudiant($etudiant_id)
    {
        $rows = $this->coursModel->getCoursByEtudiant($etudiant_id);
        $allCours = [];

        if($rows === false){
            return false;
        }

        foreach ($rows as $row) {
            $cours = new Cours();
            $cours->setCoursId($row['cours_id']);
            $cours->setTitre($row['cours_titre']);
            $cours->setDescription($row['description']);
            $cours->setFormateurId($row['formateur_id']);
            $cours->setCategorieId($row['categorie_id']);
            $cours->setCreeLe($row['cree_le']);
            $cours->setUrlImage($row['url_image']);

            $allCours[] = $cours;
        }
        return $allCours;
    }


    public function getEtudiantsByCours($cours_id)
    {
        $rows = $this->coursModel->getEtudiantsByCours($cours_id);
        $allEtudiants = [];

        if($rows === false){
            return false;
        }

        foreach ($rows as $row) {
            $etudiant = new Etudiant();
            $etudiant->setUtilisateurId($row['utilisateur_id']);
            $etudiant->setPrenom($row['prenom']);
            $etudiant->setNom($row['nom']);
            $etudiant->setEmail($row['email']);
            $etudiant->setRole($row['role']);
            $etudiant->setCreeLe($row['cree_le']);

            $allEtudiants[] = $etudiant;
        }
        return $allEtudiants;
    }


    public function getAllInscriptions(){
        $rows = $this->inscriptionModel->getAllInscriptions();

        if($rows === false){
            return false;
        }

        return $rows;
    }

    public function getInscriptionByCours($id){
        $rows = $this->inscriptionModel->getInscriptionByCours($id);

        if($rows === false){
            return false;
        }

        return $rows;
    }

    public function getInscriptionByFormateur($formateur_id){
        $rows = $this->inscriptionModel->getInscriptionByFormateur($formateur_id);
        if($rows === false){
            return false;
        }

        return $rows;
    }

    public function createInscription($etudiant_id, $cours_id)
    {
        $data = [
            'etudiant_id' => $etudiant_id,
            'cours_id' => $cours_id
        ];

        return $this->inscriptionModel->creerInscription($data);
    }

    public function supprimerInscription($id){
        return $this->inscriptionModel->supprimerInscription($id);
    }


    public function ajouterCategorie($categorie_nom)
    {
        $data = [
            'categorie_nom' => $categorie_nom
        ];

        return $this->categorieModel->creerCategorie($data);
    }


    public function getCategories()
    {
        $rows = $this->categorieModel->getAllCategories();
        
        if($rows === false){
            return false;
        }

        return $rows;
    }

    public function updateCoursByAdmin($id, $data){
        return $this->coursModel->updateCoursByAdmin($id, $data);
    }

    public function supprimerCours($id){
        return $this->coursModel->supprimerCours($id);
    }
    

    public function updateCategorie($id, $categorie_nom) {
        $data = [
            'categorie_nom' => $categorie_nom
        ];

        return $this->categorieModel->updateCategorie($id, $data);
    }


    public function supprimerCategorie($id) {
        return $this->categorieModel->supprimerCategorie($id);
    }
}

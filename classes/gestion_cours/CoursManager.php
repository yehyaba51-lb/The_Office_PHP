<?php
require_once(__DIR__ . '/../acces_donnees/BaseDeDonnee.php');
require_once(__DIR__ . '/../acces_donnees/CoursModel.php');
require_once(__DIR__ . '/../acces_donnees/LeconModel.php');
require_once(__DIR__ . '/../acces_donnees/LeconTexteModel.php');
require_once(__DIR__ . '/../acces_donnees/LeconPdfModel.php');
require_once(__DIR__ . '/../acces_donnees/LeconVideoModel.php');
require_once(__DIR__ . '/../acces_donnees/InscriptionModel.php');
require_once(__DIR__ . '/../acces_donnees/CategorieModel.php');
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

    public function ajouterLecon($cours_id, $lecon_titre, $lecon_order)
    {
        $data = [
            'cours_id' => $cours_id,
            'lecon_titre' => $lecon_titre,
            'lecon_order' => $lecon_order
        ];

        return $this->leconModel->creerLecon($data);
    }

    public function getCours($id)
    {
        $row = $this->coursModel->getCours($id);

        if (!$row) {
            return false;
        }

        return $row;
    }


    public function getAllCours()
    {
        $rows = $this->coursModel->getAllCours();
            
        if(!$rows){
            return false;
        }

        return $rows;
    }


    public function getCoursByFormateur($formateur_id)
    {
        $rows = $this->coursModel->getCoursByFormateur($formateur_id);
        $allCours = [];

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

    public function getLeconsByCours($cours_id)
    {
        $rows = $this->leconModel->getLeconsByCours($cours_id);
        $result = [];

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


    public function getLeconTextesByLecon($lecon_id)
    {
        $rows = $this->leconTexteModel->getLeconTextesByLecon($lecon_id);
        $allLeconTextes = [];

        foreach ($rows as $row) {
            $leconTexte = new LeconTexte();
            $leconTexte->setLeconId($row['lecon_id']);
            $leconTexte->setCoursId($row['cours_id']);
            $leconTexte->setTexteId($row['texte_id']);
            $leconTexte->setContenuTexte($row['contenu_texte']);
            $leconTexte->setTexteOrdre($row['texte_ordre']);

            $allLeconTextes[] = $leconTexte;
        }
        return $allLeconTextes;
    }


    public function createLeconTexte($lecon_id, $cours_id, $contenu_texte, $texte_ordre)
    {
        $data = [
            'lecon_id' => $lecon_id,
            'cours_id' => $cours_id,
            'contenu_texte' => $contenu_texte,
            'texte_ordre' => $texte_ordre
        ];

        return $this->leconTexteModel->creerLeconTexte($data);
    }


    public function getLeconPdfsByLecon($lecon_id)
    {
        $rows = $this->leconPdfModel->getLeconPdfsByLecon($lecon_id);
        $allLeconPdfs = [];

        foreach ($rows as $row) {
            $leconPdf = new LeconPdf();
            $leconPdf->setLeconId($row['lecon_id']);
            $leconPdf->setCoursId($row['cours_id']);
            $leconPdf->setPdfId($row['pdf_id']);
            $leconPdf->setUrlPdf($row['url_pdf']);
            $leconPdf->setPdfOrdre($row['pdf_ordre']);

            $allLeconPdfs[] = $leconPdf;
        }
        return $allLeconPdfs;
    }


    public function createLeconPdf($lecon_id, $cours_id, $url_pdf, $pdf_ordre)
    {
        $data = [
            'lecon_id' => $lecon_id,
            'cours_id' => $cours_id,
            'url_pdf' => $url_pdf,
            'pdf_ordre' => $pdf_ordre
        ];

        return $this->leconPdfModel->creerLeconPdf($data);
    }


    public function getLeconVideosByLecon($lecon_id)
    {
        $rows = $this->leconVideoModel->getLeconVideosByLecon($lecon_id);
        $allLeconVideos = [];

        foreach ($rows as $row) {
            $leconVideo = new LeconVideo();
            $leconVideo->setLeconId($row['lecon_id']);
            $leconVideo->setCoursId($row['cours_id']);
            $leconVideo->setVideoId($row['video_id']);
            $leconVideo->setUrlVideo($row['url_video']);
            $leconVideo->setVideoOrdre($row['video_ordre']);
            $leconVideo->setDuree($row['duree']);

            $allLeconVideos[] = $leconVideo;
        }

        return $allLeconVideos;
    }


    public function createLeconVideo($lecon_id, $cours_id, $url_video, $video_ordre, $duree)
    {
        $data = [
            'lecon_id' => $lecon_id,
            'cours_id' => $cours_id,
            'url_video' => $url_video,
            'video_ordre' => $video_ordre,
            'duree' => $duree,
        ];

        return $this->leconVideoModel->creerLeconVideo($data);
    }


    public function getCoursByEtudiant($etudiant_id)
    {
        $rows = $this->coursModel->getCoursByEtudiant($etudiant_id);
        $allCours = [];

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

        if(!$rows){
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
        
        if(!$rows){
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

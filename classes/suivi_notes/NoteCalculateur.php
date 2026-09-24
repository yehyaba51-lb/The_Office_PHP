<?php
    require_once(__DIR__ . '/../acces_donnees/BaseDeDonnee.php');
    require_once(__DIR__ . '/../acces_donnees/QuestionModel.php');
    require_once(__DIR__ . '/../acces_donnees/SoumissionModel.php');
    require_once(__DIR__ . '/../acces_donnees/ProgressionExerciceModel.php');
    require_once(__DIR__ . '/../acces_donnees/ProgressionLeconModel.php');
    require_once(__DIR__ . '/../acces_donnees/ProgressionModel.php');
    require_once(__DIR__ . '/../acces_donnees/ExerciceModel.php');
    require_once(__DIR__ . '/../acces_donnees/LeconModel.php');

    class NoteCalculateur{
        private $questionModel;
        private $soumissionModel;
        private $progressionExerciceModel;
        private $progressionLeconModel;
        private $progressionModel;
        private $exerciceModel;
        private $leconModel;

        public function __construct(){
            $db = new BaseDeDonnee();

            $this->questionModel = new QuestionModel($db);
            $this->soumissionModel = new SoumissionModel($db);
            $this->progressionExerciceModel = new ProgressionExerciceModel($db);
            $this->progressionLeconModel = new ProgressionLeconModel($db);
            $this->progressionModel = new ProgressionModel($db);
            $this->exerciceModel = new ExerciceModel($db);
            $this->leconModel = new LeconModel($db);
        }


        public function calculerMoyenneExercice($exercice_id, $etudiant_id){
            $questions_rows = $this->questionModel->getQuestionsByExercice($exercice_id);
            
            $areAllCorrected = [];
            $allNotes = [];

            if(!$questions_rows) {
                return false;
            }
            
            foreach ($questions_rows as $question) {
                $soumission_row = $this->soumissionModel->getSoumissionByEtudiantQuestion($etudiant_id, $question['question_id']);

                if($soumission_row && $soumission_row['note'] !== null){
                    $areAllCorrected[] = true;
                    $allNotes[] = $soumission_row['note'];
                } else {
                    $areAllCorrected[] = false;
                }                
            }

            if(in_array(false, $areAllCorrected)){
                return false;
            } else {
                $sum = 0;
                foreach ($allNotes as $note) {
                    $sum = $sum + $note;
                }

                $moy = $sum / count($allNotes);

                $data = [
                    'note' => $moy,
                    'exercice_id' => $exercice_id,
                    'etudiant_id' => $etudiant_id
                ];

                $this->progressionExerciceModel->updateProgressionExercice($data);

                return $moy;
            }
        }


        public function getMoyenneExercice($exercice_id, $etudiant_id){
            $row = $this->progressionExerciceModel->getProgressionByExerciceEtudiant($etudiant_id, $exercice_id);
            
            if(!$row){
                return false;
            }
            return $row['note'];
        }


        public function calculerMoyenneLecon($lecon_id, $cours_id, $etudiant_id){
            $exercice_rows = $this->exerciceModel->getExercicesByLecon($lecon_id, $cours_id);
            $allNotes = [];
            $areAllCorrected = [];

            if(!$exercice_rows){
                return false;
            }

            foreach($exercice_rows as $exercice){
                $exercice_progression_row = $this->progressionExerciceModel->getProgressionByExerciceEtudiant($etudiant_id, $exercice['exercice_id']);

                if($exercice_progression_row && $exercice_progression_row['note'] !== null){
                    $areAllCorrected[] = true;
                    $allNotes[] = $exercice_progression_row['note'];
                } else {
                    $areAllCorrected[] = false;
                }
            }

            if(in_array(false, $areAllCorrected)){
                return false;
            } else {
                $sum = 0;

                foreach ($allNotes as $note) {
                    $sum = $sum + $note;
                }

                $moy = $sum / count($allNotes);

                $data = [
                    'note' => $moy,
                    'etudiant_id' => $etudiant_id,
                    'cours_id' => $cours_id,
                    'lecon_id' => $lecon_id,
                ];

                $this->progressionLeconModel->updateProgressionLecon($data);

                return $moy;
            }
        }


        public function getMoyenneLecon($lecon_id, $cours_id, $etudiant_id){
            $row = $this->progressionLeconModel->getProgressionByLeconEtudiant($etudiant_id, $cours_id, $lecon_id);

            if(!$row){
                return false;
            }

            return $row['note'];
        }


        public function calculerNoteFinale($etudiant_id, $cours_id){
            $lecon_rows = $this->leconModel->getLeconsByCours($cours_id);
            $areAllCorrected = [];
            $allNotes = [];

            if(!$lecon_rows){
                return false;
            }

            foreach ($lecon_rows as $lecon) {
                $progression_lecon_row = $this->progressionLeconModel->getProgressionByLeconEtudiant($etudiant_id, $cours_id, $lecon['id']);

                if($progression_lecon_row && $progression_lecon_row['note'] !== null){
                    $areAllCorrected[] = true;
                    $allNotes[] = $progression_lecon_row['note'];
                } else {
                    $areAllCorrected[] = false;
                }
            }

            if(in_array(false, $areAllCorrected)){
                return false;
            } else {
                $sum = 0;

                foreach ($allNotes as $note) {
                    $sum = $sum + $note;
                }

                $noteFinale = $sum / count($allNotes);

                $data = [
                    'note_finale' => $noteFinale,
                    'etudiant_id' => $etudiant_id,
                    'cours_id' => $cours_id  
                ];

                $this->progressionModel->updateProgression($data);

                return $noteFinale;
            }
        }

        public function getNoteFinale($etudiant_id, $cours_id){
            $row = $this->progressionModel->getProgressionPerEtudiant($etudiant_id, $cours_id);

            if(!$row){
                return false;
            }

            return $row['note_finale'];
        }
    }
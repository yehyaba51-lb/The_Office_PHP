<?php
    require_once(__DIR__ . '/../acces_donnees/BaseDeDonnee.php');
    require_once(__DIR__ .'/../acces_donnees/ExerciceModel.php');
    require_once(__DIR__ .'/../acces_donnees/QuestionModel.php');
    require_once(__DIR__ .'/../acces_donnees/ChoixModel.php');
    require_once(__DIR__ .'/../acces_donnees/SoumissionModel.php');
    require_once('Exercice.php');
    require_once('Question.php');
    require_once('Choix.php');
    require_once('Soumission.php');


    class ExerciceManager{
        private $exerciceModel;
        private $questionModel;
        private $choixModel;
        private $soumissionModel;

        public function __construct()
        {
            $db = new BaseDeDonnee();

            $this->exerciceModel = new ExerciceModel($db);
            $this->questionModel = new QuestionModel($db);
            $this->choixModel = new ChoixModel($db);
            $this->soumissionModel = new SoumissionModel($db);
        }


        public function creerPlaceholder($lecon_id, $cours_id, $exercice_titre){
            $data = [
                'lecon_id' => $lecon_id,
                'cours_id' => $cours_id,
                'exercice_titre' => $exercice_titre
            ];

            return $this->exerciceModel->creerExercice($data);
        }

        public function estDeverrouille(){}


        public function getExercice($id){
            $row = $this->exerciceModel->getExercice($id);

            if(!$row){
                return false;
            }

            $exercice = new Exercice();
            $exercice->setExerciceId($row['exercice_id']);
            $exercice->setLeconId($row['lecon_id']);
            $exercice->setCoursId($row['cours_id']);
            $exercice->setTitre($row['exercice_titre']);

            return $exercice;
        }
        
        public function getExercicesByCours($cours_id){
            $rows = $this->exerciceModel->getExercicesByCours($cours_id);

            if($rows === false){
                return false;
            }

            return $rows;
        }
        public function getExercicesByLecon($lecon_id, $cours_id){
            $rows = $this->exerciceModel->getExercicesByLecon($lecon_id, $cours_id);
            $allExercices = [];

            if($rows === false){
                return false;
            }
            
            foreach ($rows as $row) {
                $exercice = new Exercice();
                $exercice->setExerciceId($row['exercice_id']);
                $exercice->setLeconId($row['lecon_id']);
                $exercice->setCoursId($row['cours_id']);
                $exercice->setTitre($row['exercice_titre']);

                $allExercices[] = $exercice;
            }
            return $allExercices;
        }

        public function getSoumissionDashboard($formateur_id){
            $rows = $this->soumissionModel->getSoumissionDashboard($formateur_id);

            if($rows === false){
                return false;
            }

            return $rows;
        }

        public function getSoumissionFormateur($formateur_id){
            $rows = $this->soumissionModel->getSoumissionFormateur($formateur_id);

            if($rows === false){
                return false;
            }

            return $rows;
        }

        public function getQuestionsByExercice($exercice_id){
            $rows = $this->questionModel->getQuestionsByExercice($exercice_id);

            if($rows === false){
                return false;
            }

            return $rows;
        }

        public function creerQuestion($data){
            $question_data = [
                'exercice_id' => $data['exercice_id'],
                'texte_question' => $data['texte_question'],
                'question_type' => $data['question_type']
            ];
            $question_id = $this->questionModel->creerQuestion($question_data);

            if($question_id === false){
                return false;
            }

            if($data['question_type'] === 'QCM' && !empty($data['choix'])){

                foreach ($data['choix'] as $index => $choix) {
                    $choix_row = [
                        'question_id' => $question_id,
                        'texte_choix' => $choix['texte'],
                        'est_correct' => $choix['correct'],
                        'ordre' => $index + 1

                    ];
                    
                    $result = $this->choixModel->creerChoix($choix_row);
                    if($result === false){
                        return false;
                    }
                }
            }
            return $question_id;
        }


        public function updateQuestion($question_id, $data){
            $question_data = [
                'exercice_id' => $data['exercice_id'],
                'texte_question' => $data['texte_question'],
                'question_type' => $data['question_type']
            ];
            $execute = $this->questionModel->updateQuestion($question_id, $question_data);

            if($execute === false){
                return false;
            }

            if($data['question_type'] === 'QCM' && !empty($data['choix'])){

                foreach ($data['choix'] as $choix) {
                    $choix_row = [
                        'texte_choix' => $choix['texte'],
                        'est_correct' => $choix['correct']

                    ];
                    
                    $result = $this->choixModel->updateChoix($choix['choix_id'], $choix_row);
                    if($result === false){
                        return false;
                    }
                }
            }

            return $execute;
        }

        
        public function supprimerQuestion($question_id){
            return $this->questionModel->supprimerQuestion($question_id);
        }

        public function getChoixByQuestion($question_id){
            $rows = $this->choixModel->getChoixByQuestion($question_id);
           
            if($rows === false){
                return false;
            }

            return $rows;
        }

        public function getChoixByExercice($exercice_id){
            $rows = $this->choixModel->getChoixByExercice($exercice_id);
           
            if($rows === false){
                return false;
            }

            return $rows;
        }

        public function createChoix($question_id, $texte_choix, $est_correct){
            $data = [
                'question_id' => $question_id,
                'texte_choix' => $texte_choix,
                'est_correct' => $est_correct
            ];

            return $this->choixModel->creerChoix($data);
        }

        public function updateChoix($choix_id, $question_id, $texte_choix, $est_correct){
            $data = [
                'question_id' => $question_id,
                'texte_choix' => $texte_choix,
                'est_correct' => $est_correct
            ];

            return $this->choixModel->updateChoix($choix_id, $data);
        }

        public function supprimerChoix($choix_id){
            return $this->choixModel->supprimerChoix($choix_id);
        }

        public function getSoumission($soumission_id){
            $row = $this->soumissionModel->getSoumission($soumission_id);

            if(!$row){
                return false;
            }

            $soumission = new Soumission();
            $soumission->setSoumissionId($row['soumission_id']);
            $soumission->setEtudiantId($row['etudiant_id']);
            $soumission->setQuestionId($row['question_id']);
            $soumission->setSoumissionReponse($row['soumission_reponse']);
            $soumission->setUrlFichier($row['url_fichier']);
            $soumission->setSoumisLe($row['soumis_le']);
            $soumission->setCorrigeLe($row['corrige_le']);
            $soumission->setCorrigePar($row['corrige_par']);
            $soumission->setNote($row['note']);
            $soumission->setCommentaire($row['commentaire']);

            return $soumission;
        }

        public function getSoumissionsByEtudiant($etudiant_id){
            $rows = $this->soumissionModel->getSoumissionsByEtudiant($etudiant_id);
            $allSoumission = [];

            if($rows === false){
                return false;
            }

            foreach($rows as $row){
                $soumission = new Soumission();
                $soumission->setSoumissionId($row['soumission_id']);
                $soumission->setEtudiantId($row['etudiant_id']);
                $soumission->setQuestionId($row['question_id']);
                $soumission->setSoumissionReponse($row['soumission_reponse']);
                $soumission->setUrlFichier($row['url_fichier']);
                $soumission->setSoumisLe($row['soumis_le']);
                $soumission->setCorrigeLe($row['corrige_le']);
                $soumission->setCorrigePar($row['corrige_par']);
                $soumission->setNote($row['note']);
                $soumission->setCommentaire($row['commentaire']);

                $allSoumission[] = $soumission;
            }
            return $allSoumission;
        }

        public function creerSoumission($etudiant_id, $question_id, $soumission_reponse, $url_fichier){
            $data = [
                'etudiant_id' => $etudiant_id,
                'question_id' => $question_id,
                'soumission_reponse' => $soumission_reponse,
                'url_fichier' => $url_fichier
            ];

            return $this->soumissionModel->creerSoumission($data);
        }

        public function corrigerSoumission($soumission_id, $formateur_id, $note, $commentaire){
            $data = [
                'corrige_par' => $formateur_id,
                'note' => $note,
                'commentaire' => $commentaire
            ];

            return $this->soumissionModel->corrigerSoumission($soumission_id, $data);
        }


        public function resoumettre($soumission_id, $nouvelle_reponse, $nouvel_url_fichier){
            $soumission = $this->getSoumission($soumission_id);

            if(!$soumission || $soumission->getNote() === null || $soumission->getNote() >= 10){
                return false;
            }

            $data = [
                'soumission_reponse' => $nouvelle_reponse,
                'url_fichier' => $nouvel_url_fichier
            ];

            return $this->soumissionModel->resoumettre($soumission_id, $data);
        }

    }
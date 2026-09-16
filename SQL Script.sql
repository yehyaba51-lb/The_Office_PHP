USE e_learning;

CREATE TABLE utilisateur(
    utilisateur_id INT PRIMARY KEY AUTO_INCREMENT,
    prenom VARCHAR(50) NOT NULL,
    nom VARCHAR(50) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    mot_de_passe VARCHAR(100) NOT NULL,
    role ENUM('Administrateur', 'Etudiant', 'Formateur'),
    cree_le TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    premiere_connexion BOOLEAN DEFAULT TRUE
);

CREATE TABLE categorie(
    categorie_id INT PRIMARY KEY AUTO_INCREMENT,
    categorie_nom VARCHAR(100)
);

CREATE TABLE cours(
    cours_id INT PRIMARY KEY AUTO_INCREMENT,
    cours_titre VARCHAR(100) NOT NULL,
    description TEXT,
    formateur_id INT,
    categorie_id INT,
    cree_le TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    url_image VARCHAR(255),

    FOREIGN KEY (formateur_id) REFERENCES utilisateur(utilisateur_id) ON DELETE SET NULL,
    FOREIGN KEY (categorie_id) REFERENCES categorie(categorie_id) ON DELETE SET NULL
);

CREATE TABLE lecon(
    lecon_id INT AUTO_INCREMENT,
    cours_id INT NOT NULL,
    lecon_titre VARCHAR(100),
    lecon_ordre INT NOT NULL,

    FOREIGN KEY (cours_id) REFERENCES cours (cours_id) ON DELETE CASCADE,

    PRIMARY Key(lecon_id, cours_id)
);

CREATE TABLE progression_lecon(
    progression_lecon_id INT PRIMARY KEY AUTO_INCREMENT,
    cours_id INT NOT NULL,
    lecon_id INT NOT NULL,
    etudiant_id INT NOT NULL,
    complete_le DATE,
    statut ENUM('en_cours', 'terminee') DEFAULT NULL,
    note FLOAT,
    UNIQUE (etudiant_id, lecon_id, cours_id),

    FOREIGN KEY (lecon_id, cours_id) REFERENCES lecon (lecon_id, cours_id) ON DELETE CASCADE,
    FOREIGN KEY (etudiant_id) REFERENCES utilisateur (utilisateur_id) ON DELETE CASCADE
);

CREATE TABLE lecon_pdf(
    pdf_id INT PRIMARY KEY AUTO_INCREMENT,
    lecon_id INT NOT NULL,
    cours_id INT NOT NULL,
    url_pdf VARCHAR(255) NOT NULL,
    pdf_order INT NOT NULL,

    FOREIGN KEY (lecon_id, cours_id) REFERENCES lecon(lecon_id, cours_id) ON DELETE CASCADE
);

CREATE TABLE lecon_texte(
    texte_id INT PRIMARY KEY AUTO_INCREMENT,
    lecon_id INT NOT NULL,
    cours_id INT NOT NULL,
    contenu_texte TEXT NOT NULL,
    texte_order INT NOT NULL,

    FOREIGN KEY (lecon_id, cours_id) REFERENCES lecon(lecon_id, cours_id) ON DELETE CASCADE
);

CREATE TABLE lecon_video(
    video_id INT PRIMARY KEY AUTO_INCREMENT,
    lecon_id INT NOT NULL,
    cours_id INT NOT NULL,
    url_video VARCHAR(255) NOT NULL,
    video_order INT NOT NULL,
    duree INT NOT NULL,

    FOREIGN KEY (lecon_id, cours_id) REFERENCES lecon(lecon_id, cours_id) ON DELETE CASCADE
);

CREATE TABLE progression(
    progression_id INT PRIMARY KEY AUTO_INCREMENT,
    etudiant_id INT NOT NULL,
    cours_id INT NOT NULL,
    complete_le DATE,
    derniere_lecon_id INT,
    modifie_le DATETIME DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE (etudiant_id, cours_id),

    FOREIGN KEY (etudiant_id) REFERENCES utilisateur(utilisateur_id) ON DELETE CASCADE,
    FOREIGN KEY (derniere_lecon_id, cours_id) REFERENCES lecon(lecon_id, cours_id) ON DELETE CASCADE
);

CREATE TABLE inscription(
    inscription_id INT PRIMARY KEY AUTO_INCREMENT,
    etudiant_id INT NOT NULL,
    cours_id INT NOT NULL,
    inscrit_le TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    note_finale FLOAT,
    UNIQUE (etudiant_id, cours_id),

    FOREIGN KEY (etudiant_id) REFERENCES utilisateur(utilisateur_id) ON DELETE CASCADE,
    FOREIGN KEY (cours_id) REFERENCES cours(cours_id) ON DELETE CASCADE
);

CREATE TABLE exercice(
    exercice_id INT PRIMARY KEY AUTO_INCREMENT,
    cours_id INT NOT NULL,
    lecon_id INT NOT NULL,
    exercice_titre VARCHAR(100) NOT NULL,

    FOREIGN KEY (lecon_id, cours_id) REFERENCES lecon(lecon_id, cours_id) ON DELETE CASCADE

);

CREATE TABLE progression_exercice(
    progression_exercice_id INT PRIMARY KEY AUTO_INCREMENT,
    etudiant_id INT NOT NULL,
    exercice_id INT NOT NULL,
    complete_le DATE,
    statut ENUM('a_faire', 'soumis', 'termine') DEFAULT NULL,
    note FLOAT,
    UNIQUE (etudiant_id, exercice_id),

    FOREIGN KEY (etudiant_id) REFERENCES utilisateur(utilisateur_id) ON DELETE CASCADE,
    FOREIGN KEY (exercice_id) REFERENCES exercice(exercice_id) ON DELETE CASCADE
);

CREATE TABLE question(
    question_id INT PRIMARY KEY AUTO_INCREMENT,
    exercice_id INT NOT NULL,
    texte_question VARCHAR(255) NOT NULL,
    question_type ENUM('Input', 'QCM', 'File Upload') DEFAULT 'Input',

    FOREIGN KEY (exercice_id) REFERENCES exercice(exercice_id) ON DELETE CASCADE
);

CREATE TABLE choix(
    choix_id INT PRIMARY KEY AUTO_INCREMENT,
    question_id INT NOT NULL,
    texte_choix VARCHAR(255) NOT NULL,
    est_correct BOOLEAN NOT NULL,

    FOREIGN KEY (question_id) REFERENCES question(question_id) ON DELETE CASCADE
);

CREATE TABLE soumission(
    soumission_id INT PRIMARY KEY AUTO_INCREMENT,
    etudiant_id INT NOT NULL,
    question_id INT NOT NULL,
    soumission_reponse VARCHAR(255),
    url_fichier VARCHAR(255),
    soumis_le DATE NOT NULL,
    corrige_le DATE,
    corrige_par INT,
    note FLOAT,
    commentaire TEXT,
    UNIQUE (etudiant_id, question_id),

    FOREIGN KEY (etudiant_id) REFERENCES utilisateur(utilisateur_id) ON DELETE CASCADE,
    FOREIGN KEY (question_id) REFERENCES question(question_id) ON DELETE CASCADE,
    FOREIGN KEY (corrige_par) REFERENCES utilisateur(utilisateur_id) ON DELETE SET NULL
);
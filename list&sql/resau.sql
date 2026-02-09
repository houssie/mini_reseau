create database reseau
use reseau
 create table reseaux(
id_membres int primary key auto_increment,
email varchar (20),
motdepasse varchar (20),
nom varchar (20),
date_naissance date
 );

CREATE TABLE publications (
    id_publication INT PRIMARY KEY AUTO_INCREMENT,
    dateheure_publication DATETIME DEFAULT CURRENT_TIMESTAMP,
    texte_publication TEXT,
    id_membres INT,
    FOREIGN KEY (id_membres) REFERENCES reseaux(id_membres)
);

-- Création de la table 'commentaires'
CREATE TABLE commentaires (
   
    id_commentaire INT PRIMARY KEY AUTO_INCREMENT,
    dateheure_commentaire DATETIME DEFAULT CURRENT_TIMESTAMP,
    texte_commentaire TEXT,
    id_membres INT,
    id_publication INT,
    FOREIGN KEY (id_membres) REFERENCES reseaux(id_membres), 
    FOREIGN KEY (id_publication) REFERENCES publications(id_publication) 
);

CREATE TABLE amis (
    id_amis INT PRIMARY KEY AUTO_INCREMENT,
    id_membre1 INT, -- ID de l'utilisateur qui envoie l'invitation
    id_membre2 INT, -- ID de l'utilisateur qui reçoit l'invitation
    statut ENUM('en_attente', 'accepte') DEFAULT 'en_attente', -- Statut de la relation
    FOREIGN KEY (id_membre1) REFERENCES reseaux(id_membres),
    FOREIGN KEY (id_membre2) REFERENCES reseaux(id_membres)
);
ALTER TABLE publications ADD COLUMN visibilite ENUM('moi', 'amis', 'tout_le_monde') DEFAULT 'tout_le_monde';
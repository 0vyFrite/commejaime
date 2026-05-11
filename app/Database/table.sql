-- Active: 1761055692295@@127.0.0.1@3306@regime_sante
DROP DATABASE IF EXISTS regime_sante;

CREATE DATABASE regime_sante;

USE regime_sante;

CREATE or replace TABLE utilisateurs (
    id_user INT PRIMARY KEY AUTO_INCREMENT,
    nom VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    mot_de_passe VARCHAR(255) NOT NULL,
    genre ENUM('homme','femme','autre') NOT NULL,
    solde_portefeuille DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    est_gold BOOLEAN NOT NULL DEFAULT FALSE,
    date_inscription TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
);
CREATE or replace TABLE profils_sante (
    id INT PRIMARY KEY AUTO_INCREMENT,
    utilisateur_id INT NOT NULL UNIQUE,
    taille_cm DECIMAL(5,2) NOT NULL,
    poids_kg DECIMAL(5,2) NOT NULL,
    imc DECIMAL(5,2) NOT NULL,
    objectif ENUM('augmenter_poids','reduire_poids','imc_ideal') NOT NULL,
    date_mesure TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (utilisateur_id)REFERENCES utilisateurs(id_user)ON DELETE CASCADE
);

CREATE or replace TABLE regimes (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nom VARCHAR(150) NOT NULL,
    description TEXT,
    pct_viande INT NOT NULL,
    pct_poisson INT NOT NULL,
    pct_volaille INT NOT NULL,
    variation_poids_kg DECIMAL(5,2) NOT NULL,
    duree_jours INT NOT NULL,
    prix DECIMAL(10,2) NOT NULL,
    CHECK (pct_viande BETWEEN 0 AND 100),
    CHECK (pct_poisson BETWEEN 0 AND 100),
    CHECK (pct_volaille BETWEEN 0 AND 100),
    CHECK (
        pct_viande + pct_poisson + pct_volaille <= 100
    )
);

CREATE or replace TABLE activites (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nom VARCHAR(150) NOT NULL,
    description TEXT,
    variation_poids_kg INT NOT NULL,
    frequence_semaine INT NOT NULL,
    duree_minutes INT NOT NULL
);

CREATE or replace TABLE user_regimes (
    id INT PRIMARY KEY AUTO_INCREMENT,
    utilisateur_id INT NOT NULL,
    regime_id INT NOT NULL,
    activite_id INT DEFAULT NULL,
    date_debut DATE NOT NULL,
    date_fin DATE NOT NULL,
    prix_paye DECIMAL(10,2) NOT NULL,
    gold_applique BOOLEAN NOT NULL DEFAULT FALSE,
    FOREIGN KEY (utilisateur_id)REFERENCES utilisateurs(id_user)ON DELETE CASCADE,
    FOREIGN KEY (regime_id)REFERENCES regimes(id),
    FOREIGN KEY (activite_id)REFERENCES activites(id)
);

CREATE or replace TABLE codes_portefeuille (
    id INT PRIMARY KEY AUTO_INCREMENT,
    code VARCHAR(50) NOT NULL UNIQUE,
    montant DECIMAL(10,2) NOT NULL,
    utilise BOOLEAN NOT NULL DEFAULT FALSE,
    utilise_par INT DEFAULT NULL,
    date_creation TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    date_utilisation DATETIME NULL,
    FOREIGN KEY (utilise_par)REFERENCES utilisateurs(id_user)
);

CREATE or replace TABLE transactions (
    id INT PRIMARY KEY AUTO_INCREMENT,
    utilisateur_id INT NOT NULL,
    code_id INT DEFAULT NULL,
    montant DECIMAL(10,2) NOT NULL,
    type_transaction ENUM('recharge_code','achat_regime','achat_gold') NOT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (utilisateur_id)REFERENCES utilisateurs(id_user),
    FOREIGN KEY (code_id)REFERENCES codes_portefeuille(id)
);

CREATE or replace TABLE options_gold (
    id INT PRIMARY KEY AUTO_INCREMENT,
    utilisateur_id INT NOT NULL UNIQUE,
    prix_paye DECIMAL(10,2) NOT NULL,
    remise_pct DECIMAL(5,2) NOT NULL DEFAULT 15.00,
    date_achat TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (utilisateur_id)REFERENCES utilisateurs(id_user)ON DELETE CASCADE
);

CREATE or replace TABLE categories_imc (
    id INT PRIMARY KEY AUTO_INCREMENT,
    seuil_min DECIMAL(5,2) NOT NULL,
    seuil_max DECIMAL(5,2),
    label VARCHAR(100) NOT NULL,
    description VARCHAR(255)
);

INSERT INTO categories_imc (seuil_min, seuil_max, label, description) VALUES
(0, 18.5, 'Insuffisance pondérale', 'Vous êtes en dessous du poids normal'),
(18.5, 25, 'Poids normal', 'Votre poids est dans la normale'),
(25, 30, 'Surpoids', 'Vous avez un léger surpoids'),
(30, 100, 'Obésité', 'Vous avez une obésité');
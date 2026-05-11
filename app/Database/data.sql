-- Utilisateurs (prenom supprimé, mot de passe = "password" hashé bcrypt fictif)
INSERT INTO utilisateurs (nom, email, mot_de_passe, genre) VALUES
('Rakoto',    'jean@mail.mg',    'rakoto1234',    'homme'),
('Rasoa',     'marie@mail.mg',   'rasoa1234',     'femme'),
('Andry',     'paul@mail.mg',    'andry1234',     'homme'),
('Rahelisoa', 'clara@mail.mg',   'rahelisoa1234', 'femme'),
('Rabe',      'luc@mail.mg',     'rabe1234',      'homme');

-- Profils de santé (imc calculé manuellement : poids / (taille_m²))
INSERT INTO profils_sante (utilisateur_id, taille_cm, poids_kg, imc, objectif) VALUES
(1, 175.0, 80.0, ROUND(80.0 / ((175.0/100) * (175.0/100)), 2), 'reduire_poids'),
(2, 162.0, 55.0, ROUND(55.0 / ((162.0/100) * (162.0/100)), 2), 'imc_ideal'),
(3, 180.0, 65.0, ROUND(65.0 / ((180.0/100) * (180.0/100)), 2), 'augmenter_poids'),
(4, 158.0, 70.0, ROUND(70.0 / ((158.0/100) * (158.0/100)), 2), 'reduire_poids'),
(5, 170.0, 90.0, ROUND(90.0 / ((170.0/100) * (170.0/100)), 2), 'reduire_poids');

-- Régimes
INSERT INTO regimes (nom, description, pct_viande, pct_poisson, pct_volaille, variation_poids_kg, duree_jours, prix) VALUES
('Régime méditerranéen',  'Riche en poisson et légumes',           10, 50, 15, -4.0, 30, 25000.00),
('Régime hyperprotéiné',  'Idéal pour la prise de masse',          40, 10, 40,  5.0, 45, 35000.00),
('Régime faible en gras', 'Réduction calorique progressive',       20, 20, 30, -6.0, 60, 40000.00),
('Régime équilibré',      'Pour maintenir un IMC optimal',         25, 25, 25,  0.0, 30, 20000.00),
('Régime détox volaille', 'Basé sur les viandes blanches légères',  5, 10, 60, -3.0, 21, 18000.00);

-- Activités sportives
INSERT INTO activites (nom, description, variation_poids_kg, frequence_semaine, duree_minutes) VALUES
('Course à pied', 'Jogging léger en extérieur',          1.5, 3, 45),
('Natation',      'Nage libre en piscine',               2.5, 3, 60),
('Vélo',          'Cyclisme en plein air ou home trainer',3, 4, 60),
('Musculation',   'Entraînement aux poids',              -1.5, 3, 60),
('Yoga',          'Stretching et relaxation',            -2.5, 5, 45);

-- Codes portefeuille (15 codes)
INSERT INTO codes_portefeuille (code, montant) VALUES
('CODE-AAA-001', 5000.00),
('CODE-BBB-002', 10000.00),
('CODE-CCC-003', 5000.00),
('CODE-DDD-004', 15000.00),
('CODE-EEE-005', 5000.00),
('CODE-FFF-006', 10000.00),
('CODE-GGG-007', 5000.00),
('CODE-HHH-008', 20000.00),
('CODE-III-009', 5000.00),
('CODE-JJJ-010', 10000.00),
('CODE-KKK-011', 5000.00),
('CODE-LLL-012', 15000.00),
('CODE-MMM-013', 5000.00),
('CODE-NNN-014', 10000.00),
('CODE-OOO-015', 5000.00);
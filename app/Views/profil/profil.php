<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mon Profil — RégimeSanté</title>
    <link rel="stylesheet" href="<?= base_url('assets/css/dashboard.css')?>">
    <link rel="stylesheet" href="<?= base_url('assets/css/profil.css')?>">
</head>
<body>

<nav>
    <div class="logo">🥗 RégimeSanté</div>
    <div class="nav-right">
        <?php if($user['est_gold'] ?? false): ?>
            <span class="badge-gold">⭐ GOLD</span>
        <?php endif; ?>
        <span><?= esc($user['nom']) ?></span>
        <a href="/dashboard"><button class="btn-nav">📊 Dashboard</button></a>
        <a href="/logout"><button class="btn-logout">Déconnexion</button></a>
    </div>
</nav>

<div class="page">

    <?php if(session()->getFlashdata('succes')): ?>
        <div class="flash-succes"><?= esc(session()->getFlashdata('succes')) ?></div>
    <?php endif; ?>
    <?php if(session()->getFlashdata('erreur')): ?>
        <div class="flash-erreur"><?= esc(session()->getFlashdata('erreur')) ?></div>
    <?php endif; ?>

    <!-- EN-TÊTE PROFIL -->
    <div class="profil-header">
        <div class="avatar">
            <?php
                $initiale = mb_strtoupper(mb_substr($user['nom'], 0, 1));
                echo $initiale;
            ?>
        </div>
        <div class="profil-info">
            <h1><?= esc($userComplet['nom']) ?></h1>
            <p class="profil-email">📧 <?= esc($userComplet['email']) ?></p>
            <div class="profil-badges">
                <span class="badge-genre">
                    <?php
                        $genres = ['homme' => '👨 Homme', 'femme' => '👩 Femme', 'autre' => '🧑 Autre'];
                        echo $genres[$userComplet['genre']] ?? $userComplet['genre'];
                    ?>
                </span>
                <?php if($userComplet['est_gold']): ?>
                    <span class="badge-gold-large">⭐ Membre Gold — 15% de remise</span>
                <?php else: ?>
                    <span class="badge-standard">🔵 Membre Standard</span>
                <?php endif; ?>
                <span class="badge-date">
                    📅 Inscrit le <?= date('d/m/Y', strtotime($userComplet['date_inscription'])) ?>
                </span>
            </div>
        </div>
    </div>

    <!-- GRILLE PRINCIPALE -->
    <div class="profil-grid">

        <!-- COLONNE GAUCHE -->
        <div class="col-left">

            <!-- STATISTIQUES SANTÉ -->
            <div class="card">
                <h2 class="card-titre">📊 Mes Statistiques de Santé</h2>
                <?php if($profil): ?>
                    <div class="sante-grid">
                        <div class="sante-item">
                            <div class="sante-label">Taille</div>
                            <div class="sante-valeur bleu"><?= esc($profil['taille_cm']) ?> <small>cm</small></div>
                        </div>
                        <div class="sante-item">
                            <div class="sante-label">Poids</div>
                            <div class="sante-valeur bleu"><?= esc($profil['poids_kg']) ?> <small>kg</small></div>
                        </div>
                        <div class="sante-item">
                            <div class="sante-label">IMC</div>
                            <div class="sante-valeur vert"><?= esc($profil['imc']) ?></div>
                        </div>
                        <div class="sante-item">
                            <div class="sante-label">Catégorie IMC</div>
                            <div class="sante-valeur" style="font-size:1rem;margin-top:.4rem;">
                                <?php if($categorieActuelle): ?>
                                    <span class="categorie-badge">
                                        <?= esc($categorieActuelle['label']) ?>
                                    </span>
                                <?php else: ?>
                                    <span>—</span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <!-- JAUGE IMC -->
                    <div class="imc-gauge-section">
                        <p class="gauge-label">Jauge IMC</p>
                        <div class="imc-gauge">
                            <div class="gauge-bar">
                                <div class="gauge-zone maigreur" title="< 18.5 Maigreur"></div>
                                <div class="gauge-zone normal" title="18.5–25 Normal"></div>
                                <div class="gauge-zone surpoids" title="25–30 Surpoids"></div>
                                <div class="gauge-zone obesite" title="> 30 Obésité"></div>
                                <?php
                                    // Position de l'indicateur : IMC entre 10 et 40
                                    $imcVal = min(max((float)$profil['imc'], 10), 40);
                                    $pct = (($imcVal - 10) / 30) * 100;
                                ?>
                                <div class="gauge-indicator" style="left: <?= $pct ?>%">
                                    <div class="indicator-dot"></div>
                                    <div class="indicator-label"><?= $profil['imc'] ?></div>
                                </div>
                            </div>
                            <div class="gauge-labels">
                                <span>10</span>
                                <span>18.5</span>
                                <span>25</span>
                                <span>30</span>
                                <span>40</span>
                            </div>
                        </div>
                    </div>

                    <!-- OBJECTIF -->
                    <div class="objectif-section">
                        <p class="objectif-titre">🎯 Mon Objectif</p>
                        <?php
                            $obj = $profil['objectif'] ?? '';
                            $objData = [
                                'reduire_poids'   => ['classe' => 'reduire',   'label' => '⬇ Réduire le poids',  'desc' => 'Vous souhaitez perdre du poids progressivement.'],
                                'augmenter_poids' => ['classe' => 'augmenter', 'label' => '⬆ Augmenter le poids','desc' => 'Vous souhaitez prendre de la masse.'],
                                'imc_ideal'       => ['classe' => 'ideal',     'label' => '🎯 IMC idéal',         'desc' => 'Vous souhaitez maintenir un IMC équilibré.'],
                            ];
                            $od = $objData[$obj] ?? ['classe' => 'ideal', 'label' => $obj, 'desc' => ''];
                        ?>
                        <span class="objectif-badge <?= $od['classe'] ?>"><?= $od['label'] ?></span>
                        <p class="objectif-desc"><?= $od['desc'] ?></p>
                        <p class="profil-date-mesure">
                            📅 Dernière mesure : <?= date('d/m/Y', strtotime($profil['date_mesure'])) ?>
                        </p>
                    </div>

                <?php else: ?>
                    <p class="no-data">Aucun profil de santé enregistré.</p>
                <?php endif; ?>
            </div>

        </div>

        <!-- COLONNE DROITE -->
        <div class="col-right">

            <!-- PORTEFEUILLE -->
            <div class="card card-portefeuille">
                <h2 class="card-titre">💳 Mon Portefeuille</h2>
                <div class="solde-display">
                    <div class="solde-montant">
                        <?= number_format($userComplet['solde_portefeuille'] ?? 0, 0, ',', ' ') ?>
                        <small>Ar</small>
                    </div>
                    <div class="solde-label">Solde disponible</div>
                </div>

                <div class="recharge-section">
                    <p class="recharge-titre">🔑 Recharger avec un code</p>
                    <form method="POST" action="/portefeuille/recharger">
                        <?= csrf_field() ?>
                        <div class="recharge-form">
                            <input
                                type="text"
                                name="code"
                                placeholder="Ex: CODE-AAA-001"
                                class="input-code"
                                autocomplete="off"
                                required>
                            <button type="submit" class="btn-recharger">
                                ➕ Recharger
                            </button>
                        </div>
                    </form>
                    <p class="recharge-hint">
                        💡 Entrez un code de recharge valide pour créditer votre portefeuille.
                    </p>
                </div>
            </div>

            <!-- OPTION GOLD -->
            <div class="card card-gold <?= $userComplet['est_gold'] ? 'gold-active' : '' ?>">
                <h2 class="card-titre">⭐ Option Gold</h2>
                <?php if($userComplet['est_gold']): ?>
                    <div class="gold-actif">
                        <div class="gold-icon">👑</div>
                        <p class="gold-statut">Vous êtes membre <strong>Gold</strong> !</p>
                        <p class="gold-avantage">✅ 15% de remise sur tous les régimes</p>
                        <p class="gold-avantage">✅ Accès prioritaire aux nouveaux régimes</p>
                        <p class="gold-avantage">✅ Badge exclusif affiché sur votre profil</p>
                    </div>
                <?php else: ?>
                    <div class="gold-promo">
                        <div class="gold-avantages-liste">
                            <p>🌟 <strong>Avantages Gold :</strong></p>
                            <p class="gold-avantage-item">✅ 15% de remise sur tous les régimes</p>
                            <p class="gold-avantage-item">✅ Accès prioritaire aux nouveaux régimes</p>
                            <p class="gold-avantage-item">✅ Badge Gold sur votre profil</p>
                        </div>
                        <div class="gold-prix-box">
                            <span class="gold-prix">50 000 Ar</span>
                            <span class="gold-prix-label">paiement unique</span>
                        </div>
                        <?php
                            $solde = $userComplet['solde_portefeuille'] ?? 0;
                            $prixGold = 50000;
                            $soldeInsuffisant = $solde < $prixGold;
                        ?>
                        <form method="POST" action="/gold/acheter">
                            <?= csrf_field() ?>
                            <button type="submit" class="btn-gold-profil" <?= $soldeInsuffisant ? 'disabled title="Solde insuffisant"' : '' ?>>
                                <?= $soldeInsuffisant ? '⚠️ Solde insuffisant' : '⭐ Acheter Gold' ?>
                            </button>
                        </form>
                        <?php if($soldeInsuffisant): ?>
                            <p class="gold-insuffisant-msg">
                                Il vous manque <?= number_format($prixGold - $solde, 0, ',', ' ') ?> Ar.
                                Rechargez votre portefeuille ci-dessus.
                            </p>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>

            <!-- HISTORIQUE RAPIDE (placeholder) -->
            <div class="card">
                <h2 class="card-titre">📈 Résumé</h2>
                <div class="resume-grid">
                    <div class="resume-item">
                        <div class="resume-valeur"><?= number_format($userComplet['solde_portefeuille'] ?? 0, 0, ',', ' ') ?> Ar</div>
                        <div class="resume-label">Solde portefeuille</div>
                    </div>
                    <div class="resume-item">
                        <div class="resume-valeur <?= $userComplet['est_gold'] ? 'vert' : 'gris' ?>">
                            <?= $userComplet['est_gold'] ? '⭐ Gold' : 'Standard' ?>
                        </div>
                        <div class="resume-label">Statut compte</div>
                    </div>
                    <div class="resume-item">
                        <div class="resume-valeur bleu"><?= esc($profil['imc'] ?? '—') ?></div>
                        <div class="resume-label">IMC actuel</div>
                    </div>
                    <div class="resume-item">
                        <div class="resume-valeur"><?= esc($profil['poids_kg'] ?? '—') ?> kg</div>
                        <div class="resume-label">Poids</div>
                    </div>
                </div>
                <div style="margin-top:1.5rem;text-align:center;">
                    <a href="/dashboard" class="btn-vers-dashboard">
                        📊 Voir mes régimes recommandés →
                    </a>
                </div>
            </div>

        </div>
    </div>
</div>

<footer>
    RégimeSanté par ETU004028 - ETU004162 - ETU004374
</footer>

</body>
</html>
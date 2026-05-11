<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mon Dashboard</title>
    <link rel="stylesheet" href="<?= base_url('assets/css/dashboard.css')?>">
    <link rel="stylesheet" href="<?= base_url('assets/css/regimes-section.css')?>">
</head>
<body>
<nav>
    <div class="logo">🥗 RégimeSanté</div>
    <div class="nav-right">
        <?php if($user['est_gold']??false){?>
            <span class="badge-gold">⭐ GOLD</span>
        <?php }?>
        <span><?= esc($user['nom'])?></span>
        <a href="/logout">
            <button class="btn-logout">Déconnexion</button>
        </a>
    </div>
</nav>
<div class="page">
    <?php if(session()->getFlashdata('succes')){?>
        <div class="flash-succes">
            <?= esc(session()->getFlashdata('succes'))?>
        </div>
    <?php }?>
    <?php if(session()->getFlashdata('erreur')){?>
        <div class="flash-erreur">
            <?= esc(session()->getFlashdata('erreur'))?>
        </div>
    <?php }?>
    <p class="bienvenue">
        Bonjour, <span><?= esc($user['nom'])?></span> 👋
    </p>
    <div class="stats">
        <div class="stat-card">
            <div class="label">Mon IMC</div>
            <div class="valeur vert">
                <?= esc($profil['imc'] ?? '—')?>
            </div>
            <div class="sous">
                <?php
                    $imc = $profil['imc'] ?? 0;
                    $categorie = null;
                    foreach($categoriesIMC as $cat):
                        if($imc >= $cat['seuil_min'] && $imc < $cat['seuil_max']):
                            $categorie = $cat;
                            break;
                        endif;
                    endforeach;
                    echo $categorie ? esc($categorie['label']) : '—';
                ?>
            </div>
        </div>
        <!-- POIDS -->
        <div class="stat-card">
            <div class="label">Poids</div>
            <div class="valeur bleu">
                <?= esc($profil['poids_kg'] ?? '—')?>
                <small>kg</small>
            </div>
            <div class="sous">
                Taille : <?= esc($profil['taille_cm'] ?? '—')?> cm
            </div>
        </div>
        <!-- SOLDE -->
        <div class="stat-card">
            <div class="label">Solde portefeuille</div>
            <div class="valeur">
                <?= number_format($user['solde_portefeuille'] ?? 0, 0, ',', ' ')?>
                <small>Ar</small>
            </div>
        </div>
        <!-- OBJECTIF -->
        <div class="stat-card">
            <div class="label">Objectif</div>
            <div style="margin-top:.5rem;">
                <?php
                    $obj = $profil['objectif'] ?? '';
                    $classe = 'ideal';
                    $label  = '🎯 IMC idéal';
                    if($obj == 'reduire_poids'){
                        $classe = 'reduire';
                        $label  = '⬇ Réduire le poids';
                    }
                    if($obj == 'augmenter_poids'){
                        $classe = 'augmenter';
                        $label  = '⬆ Augmenter le poids';
                    }?>
                <span class="objectif-badge <?= $classe?>">
                    <?= $label?>
                </span>
            </div>
        </div>
    </div>
    <!-- GOLD -->
    <?php if(!$user['est_gold'] ?? false){?>
        <div class="gold-box">
            <div class="gold-texte">
                <h3>⭐ Passez à l'option Gold</h3>
                <p>
                    Profitez de <strong>15% de remise</strong>
                    sur tous les régimes.
                </p>
            </div>
            <form method="POST" action="/gold/acheter">
                <?= csrf_field()?>
                <button type="submit" class="btn-gold">
                    Acheter Gold — 50 000 Ar
                </button>
            </form>
        </div>
    <?php }?>

    <!-- RECHARGE -->
    <p class="section-titre">💳 Recharger mon portefeuille</p>
    <div class="portefeuille-box">
        <form method="POST" action="/portefeuille/recharger">
            <?= csrf_field()?>
            <input
                type="text"
                name="code"
                placeholder="Entrer un code"
                required>
            <button type="submit" class="btn-recharger">
                Recharger
            </button>
        </form>
    </div>
    <br><br>
    <!-- RÉGIMES RECOMMANDÉS -->
    <p class="section-titre">🎯 Régimes recommandés</p>
    <div class="regimes-recommandes">
        <?php if(!empty($regimes)): ?>
            <?php foreach($regimes as $index => $item): ?>
                <div class="regime-card">
                    <div class="regime-header">
                        <h3><?= esc($item['regime']['nom']) ?></h3>
                        <!-- <span class="regime-percentage">
                            <?= esc($item['pourcentage']) ?>% du traitement
                        </span> -->
                    </div>
                    <div class="regime-description">
                        <p><?= esc($item['regime']['description']) ?></p>
                    </div>
                    <div class="regime-details">
                        <div class="detail-group">
                            <span class="detail-label">Composition viandes</span>
                            <div class="composition-bars">
                                <?php 
                                    $viandes = [
                                        ['label' => 'Viande', 'pct' => $item['regime']['pct_viande']],
                                        ['label' => 'Poisson', 'pct' => $item['regime']['pct_poisson']],
                                        ['label' => 'Volaille', 'pct' => $item['regime']['pct_volaille']]
                                    ];
                                    foreach($viandes as $v):
                                ?>
                                    <div class="bar-item">
                                        <small><?= $v['label'] ?>: <?= $v['pct'] ?>%</small>
                                        <div class="bar-background">
                                            <div class="bar-fill" style="width: <?= $v['pct'] ?>%"></div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        <div class="detail-group">
                            <span class="detail-label">Variations et durée</span>
                            <div class="detail-items">
                                <div class="detail-item">
                                    <span>Variation:</span>
                                    <strong>
                                        <?php if ($item['regime']['variation_poids_kg'] > 0) echo '+' ?><?= esc($item['regime']['variation_poids_kg']) ?> kg
                                    </strong>
                                </div>
                                <div class="detail-item">
                                    <span>Durée:</span>
                                    <strong><?= esc($item['jours_calcules']) ?> jours</strong>
                                </div>
                            </div>
                        </div>
                        <div class="detail-group">
                            <span class="detail-label">Activité</span>
                            <div class="detail-items">
                                <div class="detail-item">
                                    <span>Type:</span>
                                    <strong><?= esc($item['activite']['nom']) ?></strong>
                                </div>
                                <div class="detail-item">
                                    <span>Description:</span>
                                    <strong><?= esc($item['activite']['description']) ?></strong>
                                </div>
                                <div class="detail-item">
                                    <span>Variation:</span>
                                    <strong><?= esc($item['activite']['variation_poids_kg']) ?> kg</strong>
                                </div>
                                <div class="detail-item">
                                    <span>Fréquence:</span>
                                    <strong><?= esc($item['activite']['frequence_semaine']) ?> fois par semaine</strong>
                                </div>
                                <div class="detail-item">
                                    <span>Durée:</span>
                                    <strong><?= esc($item['activite']['duree_minutes']) ?> minutes</strong>
                                </div>
                            </div>
                        </div>
                        <div class="detail-group">
                            <span class="detail-label">Prix</span>
                            <div class="detail-items">
                                <div class="detail-item">
                                    <span>Prix:</span>
                                    <strong><?= number_format($item['regime']['prix'], 0, ',', ' ') ?> Ar</strong>
                                </div>
                            </div>
                        </div>
                    </div>
                    <button class="btn-export-pdf" onclick="exportRegimePdf('<?= esc($item['regime']['id']) ?>', '<?= esc($item['activite']['id']) ?>', '<?= esc($item['pourcentage']) ?>')">
                        📥 Exporter en PDF
                    </button>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p class="no-regimes">Aucun régime recommandé pour votre objectif.</p>
        <?php endif; ?>
    </div>
</div>

<footer>
    RégimeSanté par ETU004028 - ETU004162 - ETU004374
</footer>

<script>
function exportRegimePdf(regimeId, activiteId, pourcentage) {
    // Créer une forme pour soumettre la requête d'export
    const form = document.createElement('form');
    form.method = 'GET';
    form.action = `/regimes/export/${regimeId}/${activiteId}/${pourcentage}`;
    form.style.display = 'none';
    
    document.body.appendChild(form);
    form.submit();
    document.body.removeChild(form);
}
</script>

</body>
</html>
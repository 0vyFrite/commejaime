<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mon Dashboard — RégimeSanté</title>
    <link rel="stylesheet" href="<?= base_url('assets/css/dashboard.css')?>">
    <link rel="stylesheet" href="<?= base_url('assets/css/regimes-section.css')?>">
</head>
<body>

<nav>
    <div class="logo">🥗 RégimeSanté</div>
    <div class="nav-right">
        <?php if($user['est_gold'] ?? false): ?>
            <span class="badge-gold">⭐ GOLD</span>
        <?php endif; ?>
        <span><?= esc($user['nom']) ?></span>
        <a href="/profil"><button class="btn-profil-nav">👤 Mon Profil</button></a>
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

    <p class="bienvenue">Bonjour, <span><?= esc($user['nom']) ?></span> 👋</p>

    <!-- STATS -->
    <div class="stats">
        <div class="stat-card">
            <div class="label">Mon IMC</div>
            <div class="valeur vert"><?= esc($profil['imc'] ?? '—') ?></div>
            <div class="sous">
                <?php
                    $imc = $profil['imc'] ?? 0;
                    $categorie = null;
                    foreach($categoriesIMC as $cat):
                        if($imc >= $cat['seuil_min'] && $imc < $cat['seuil_max']):
                            $categorie = $cat; break;
                        endif;
                    endforeach;
                    echo $categorie ? esc($categorie['label']) : '—';
                ?>
            </div>
        </div>
        <div class="stat-card">
            <div class="label">Poids</div>
            <div class="valeur bleu"><?= esc($profil['poids_kg'] ?? '—') ?> <small>kg</small></div>
            <div class="sous">Taille : <?= esc($profil['taille_cm'] ?? '—') ?> cm</div>
        </div>
        <div class="stat-card">
            <div class="label">Solde portefeuille</div>
            <div class="valeur">
                <?= number_format($user['solde_portefeuille'] ?? 0, 0, ',', ' ') ?>
                <small>Ar</small>
            </div>
        </div>
        <div class="stat-card">
            <div class="label">Objectif</div>
            <div style="margin-top:.5rem;">
                <?php
                    $obj = $profil['objectif'] ?? '';
                    $classe = 'ideal'; $label = '🎯 IMC idéal';
                    if($obj == 'reduire_poids')   { $classe = 'reduire';   $label = '⬇ Réduire le poids'; }
                    if($obj == 'augmenter_poids') { $classe = 'augmenter'; $label = '⬆ Augmenter le poids'; }
                ?>
                <span class="objectif-badge <?= $classe ?>"><?= $label ?></span>
            </div>
        </div>
    </div>

    <!-- GOLD -->
    <?php if(!($user['est_gold'] ?? false)): ?>
        <div class="gold-box">
            <div class="gold-texte">
                <h3>⭐ Passez à l'option Gold</h3>
                <p>Profitez de <strong>15% de remise</strong> sur tous les régimes.</p>
            </div>
            <a href="/profil">
                <button class="btn-gold">Acheter Gold — 50 000 Ar</button>
            </a>
        </div>

div {
    border: 1px solid red;
}
    <?php endif; ?>

    <!-- RECHARGE -->
    <p class="section-titre">💳 Recharger mon portefeuille</p>
    <div class="portefeuille-box">
        <form method="POST" action="/portefeuille/recharger">
            <?= csrf_field() ?>
            <input type="text" name="code" placeholder="Entrer un code (ex: CODE-AAA-001)" required>
            <button type="submit" class="btn-recharger">Recharger</button>
        </form>
    </div>

    <br>

    <!-- RÉGIMES EN PAGINATION -->
    <div class="regimes-section-header">          <div class="valeur">
                <?= number_format($user['solde_portefeuille'] ?? 0, 0, ',', ' ') ?>
                <small>Ar</small>
            </div>
        </div>
        <div class="stat-card">
            <div class="label">Objectif</div>
            <div style="margin-top:.5rem;">
        <p class="section-titre" style="margin-bottom:0;">🎯 Régimes recommandés</p>
        <?php if(!empty($regimes)): ?>
            <span class="regimes-count"><?= count($regimes) ?> régime<?= count($regimes) > 1 ? 's' : '' ?> trouvé<?= count($regimes) > 1 ? 's' : '' ?></span>
        <?php endif; ?>
    </div>

    <?php if(!empty($regimes)): ?>
        <?php
            $parPage = 3;
            $total   = count($regimes);
            $nbPages = (int)ceil($total / $parPage);
        ?>

        <div class="regimes-pager">

            <?php for($p = 0; $p < $nbPages; $p++): ?>
                <div class="regimes-page" id="page-<?= $p ?>" <?= $p > 0 ? 'style="display:none;"' : '' ?>>
                    <div class="regimes-page-grille">
                        <?php
                            $debut = $p * $parPage;
                            $slice = array_slice($regimes, $debut, $parPage);
                        ?>
                        <?php foreach($slice as $item): ?>
                            <div class="regime-card">
                                <div class="regime-header">
                                    <h3><?= esc($item['regime']['nom']) ?></h3>
                                    <?php if($user['est_gold'] ?? false): ?>
                                        <span class="regime-gold-badge">⭐ -15%</span>
                                    <?php endif; ?>
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
                                                    ['label' => 'Viande',  'pct' => $item['regime']['pct_viande']],
                                                    ['label' => 'Poisson', 'pct' => $item['regime']['pct_poisson']],
                                                    ['label' => 'Volaille','pct' => $item['regime']['pct_volaille']],
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
                                                <strong><?php if($item['regime']['variation_poids_kg'] > 0) echo '+' ?><?= esc($item['regime']['variation_poids_kg']) ?> kg</strong>
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
                                            <div class="detail-item"><span>Type:</span><strong><?= esc($item['activite']['nom']) ?></strong></div>
                                            <div class="detail-item"><span>Description:</span><strong><?= esc($item['activite']['description']) ?></strong></div>
                                            <div class="detail-item"><span>Variation:</span><strong><?= esc($item['activite']['variation_poids_kg']) ?> kg</strong></div>
                                            <div class="detail-item"><span>Fréquence:</span><strong><?= esc($item['activite']['frequence_semaine']) ?> fois/semaine</strong></div>
                                            <div class="detail-item"><span>Durée:</span><strong><?= esc($item['activite']['duree_minutes']) ?> min</strong></div>
                                        </div>
                                    </div>
                                    <div class="detail-group">
                                        <span class="detail-label">Prix</span>
                                        <div class="detail-items">
                                            <?php if($user['est_gold'] ?? false): ?>
                                                <?php $prixReduit = $item['regime']['prix'] * 0.85; ?>
                                                <div class="detail-item">
                                                    <span>Prix normal:</span>
                                                    <strong class="prix-barre"><?= number_format($item['regime']['prix'], 0, ',', ' ') ?> Ar</strong>
                                                </div>
                                                <div class="detail-item">
                                                    <span>Prix Gold (-15%):</span>
                                                    <strong class="prix-gold"><?= number_format($prixReduit, 0, ',', ' ') ?> Ar</strong>
                                                </div>
                                            <?php else: ?>
                                                <div class="detail-item">
                                                    <span>Prix:</span>
                                                    <strong><?= number_format($item['regime']['prix'], 0, ',', ' ') ?> Ar</strong>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                                <button class="btn-export-pdf"
                                    onclick="exportRegimePdf('<?= esc($item['regime']['id']) ?>', '<?= esc($item['activite']['id']) ?>', '<?= esc($item['pourcentage']) ?>')">
                                    📥 Exporter en PDF
                                </button>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endfor; ?>

            <?php if($nbPages > 1): ?>
            <div class="pagination-controls">
                <button class="btn-page btn-prev" id="btn-prev" onclick="changerPage(-1)" disabled>← Précédent</button>
                <div class="page-indicators" id="page-indicators">
                    <?php for($p = 0; $p < $nbPages; $p++): ?>
                        <button class="page-dot <?= $p === 0 ? 'active' : '' ?>" onclick="allerPage(<?= $p ?>)"><?= $p+1 ?></button>
                    <?php endfor; ?>
                </div>
                <div class="page-info">
                    Régimes <span id="debut-affiche">1</span>–<span id="fin-affiche"><?= min($parPage, $total) ?></span> sur <?= $total ?>
                </div>
                <button class="btn-page btn-next" id="btn-next" onclick="changerPage(1)" <?= $nbPages <= 1 ? 'disabled' : '' ?>>Suivant →</button>
            </div>
            <?php endif; ?>

        </div>

    <?php else: ?>
        <p class="no-regimes">Aucun régime recommandé pour votre objectif actuel.</p>
    <?php endif; ?>

</div>

<footer>
    RégimeSanté par ETU004028 - ETU004162 - ETU004374
</footer>

<style>
.btn-profil-nav {
    background: rgba(255,255,255,.15);
    border: none;
    color: #fff;
    padding: .45rem .9rem;
    border-radius: 6px;
    cursor: pointer;
}
.btn-profil-nav:hover { background: rgba(255,255,255,.3); }

.regimes-section-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 1rem;
    flex-wrap: wrap;
    gap: .5rem;
}
.regimes-count {
    background: #eff6ff;
    color: #2563eb;
    font-size: .8rem;          <div class="valeur">
                <?= number_format($user['solde_portefeuille'] ?? 0, 0, ',', ' ') ?>
                <small>Ar</small>
            </div>
        </div>
        <div class="stat-card">
            <div class="label">Objectif</div>
            <div style="margin-top:.5rem;">
    font-weight: 600;
    padding: .3rem .75rem;
    border-radius: 999px;
}

.regimes-pager { margin-bottom: 2rem; }

.regimes-page-grille {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(340px, 1fr));
    gap: 20px;
    margin-bottom: 1.5rem;
    animation: fadeIn .3s ease;
}
@keyframes fadeIn {
    from { opacity: 0; transform: translateY(8px); }
    to   { opacity: 1; transform: translateY(0); }
}

.pagination-controls {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 1rem;
    flex-wrap: wrap;
    padding: 1.25rem;
    background: #fff;
    border-radius: 12px;
    box-shadow: 0 2px 8px rgba(0,0,0,.06);
}

.btn-page {
    background: #4f46e5;
    color: #fff;
    border: none;          <div class="valeur">
                <?= number_format($user['solde_portefeuille'] ?? 0, 0, ',', ' ') ?>
                <small>Ar</small>
            </div>
        </div>
        <div class="stat-card">
            <div class="label">Objectif</div>
            <div style="margin-top:.5rem;">
    padding: .6rem 1.4rem;
    border-radius: 8px;
    cursor: pointer;
    font-weight: 600;
    font-size: .9rem;
    transition: all .2s;
}
.btn-page:hover:not(:disabled) { background: #3730a3; transform: translateY(-1px); }
.btn-page:disabled { background: #e2e8f0; color: #94a3b8; cursor: not-allowed; transform: none; }

.page-indicators { display: flex; gap: .4rem; align-items: center; }
.page-dot {
    width: 36px;
    height: 36px;
    border-radius: 8px;
    border: 2px solid #e2e8f0;
    background: #fff;
    color: #64748b;
    font-weight: 600;
    font-size: .85rem;
    cursor: pointer;
    transition: all .2s;
}
.page-dot:hover { border-color: #4f46e5; color: #4f46e5; }
.page-dot.active { background: #4f46e5; border-color: #4f46e5; color: #fff; }

.page-info { font-size: .85rem; color: #64748b; white-space: nowrap; }

.regime-gold-badge {
    background: linear-gradient(90deg, #fde68a, #fbbf24);
    color: #78350f;
    padding: 3px 10px;
    border-radius: 999px;
    font-size: .75rem;
    font-weight: 700;
    white-space: nowrap;          <div class="valeur">
                <?= number_format($user['solde_portefeuille'] ?? 0, 0, ',', ' ') ?>
                <small>Ar</small>
            </div>
        </div>
        <div class="stat-card">
            <div class="label">Objectif</div>
            <div style="margin-top:.5rem;">
}
.prix-barre { text-decoration: line-through; color: #9ca3af !important; }
.prix-gold  { color: #d97706 !important; font-size: 1rem; }

.no-regimes {
    text-align: center;
    padding: 3rem;
    color: #7f8c8d;
    background: #f8f9fa;
    border-radius: 12px;
}
</style>

<script>
    var nbPages = <?= $nbPages ?? 1 ?>;
    var parPage = <?= $parPage ?? 2 ?>;
    var total   = <?= $total ?? 0 ?>;
    var pageCourante = 0;

    function afficherPage(p) {
        for (var i = 0; i < nbPages; i++) {
            var el = document.getElementById('page-' + i);
            if (el) el.style.display = 'none';
        }
        var cible = document.getElementById('page-' + p);
        if (cible) {
            cible.style.display = 'block';
            var grille = cible.querySelector('.regimes-page-grille');
            if (grille) {
                grille.style.animation = 'none';
                grille.offsetHeight;
                grille.style.animation = 'fadeIn .3s ease';
            }
        }
        var prev = document.getElementById('btn-prev');
        var next = document.getElementById('btn-next');
        if (prev) prev.disabled = (p === 0);
        if (next) next.disabled = (p === nbPages - 1);

        document.querySelectorAll('.page-dot').forEach(function(dot, i) {
            dot.classList.toggle('active', i === p);
        });

        var debut = document.getElementById('debut-affiche');
        var fin   = document.getElementById('fin-affiche');
        if (debut) debut.textContent = p * parPage + 1;
        if (fin)   fin.textContent   = Math.min((p + 1) * parPage, total);

        pageCourante = p;
        document.querySelector('.regimes-section-header').scrollIntoView({ behavior: 'smooth', block: 'start' });
    }

    function changerPage(delta) {
        var nouvelle = pageCourante + delta;
        if (nouvelle >= 0 && nouvelle < nbPages) afficherPage(nouvelle);
    }

    function allerPage(p) { afficherPage(p); }

    function exportRegimePdf(regimeId, activiteId, pourcentage) {
        var form = document.createElement('form');
        form.method = 'GET';
        form.action = '/regimes/export/' + regimeId + '/' + activiteId + '/' + pourcentage;
        form.style.display = 'none';
        document.body.appendChild(form);
        form.submit();
        document.body.removeChild(form);
    }
</script>

</body>
</html>
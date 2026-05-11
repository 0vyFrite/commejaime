<!-- CETTE VUE EST OBSOLÈTE - Le système de régimes a été refactorisé -->
<!-- Les régimes sont maintenant affichés directement dans le dashboard avec leur pourcentage calculé -->
<!-- Pour exporter un régime en PDF, utilisez la nouvelle méthode exportRegimePdf() -->
<?php
throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Détails du régime</title>
    <link rel="stylesheet" href="<?= base_url('assets/css/regime-details.css')?>">
</head>
<body>
<nav>
    <div class="logo">🥗 RégimeSanté</div>
    <div class="nav-right">
        <a href="/dashboard">← Retour au dashboard</a>
        <span><?= esc($user['nom'])?></span>
        <a href="/logout">
            <button class="btn-logout">Déconnexion</button>
        </a>
    </div>
</nav>
<div class="page">
    <h1>Détails de votre plan régimes</h1>
    
    <div class="regimes-list">
        <?php foreach($details as $item): ?>
            <div class="regime-item">
                <div class="regime-item-header">
                    <h3><?= esc($item['regime']['nom']) ?></h3>
                    <span class="repetition">×<?= esc($item['repetitions']) ?></span>
                </div>
                <div class="regime-item-content">
                    <div class="info-group">
                        <span class="label">Description:</span>
                        <span class="value"><?= esc($item['regime']['description'] ?? '—')?></span>
                    </div>
                    <div class="info-group">
                        <span class="label">Variation de poids:</span>
                        <span class="value"><?= esc($item['poids_total']) ?> kg</span>
                    </div>
                    <div class="info-group">
                        <span class="label">Durée pour <?= esc($item['repetitions']) ?> cycle(s):</span>
                        <span class="value"><?= esc($item['temps_total']) ?> jours</span>
                    </div>
                    <div class="info-group">
                        <span class="label">Prix pour <?= esc($item['repetitions']) ?> cycle(s):</span>
                        <span class="value"><?= number_format($item['prix_total'], 0, ',', ' ') ?> Ar</span>
                    </div>
                    <div class="info-group">
                        <span class="label">Composition:</span>
                        <span class="value">
                            Viande: <?= esc($item['regime']['pct_viande']) ?>% |
                            Poisson: <?= esc($item['regime']['pct_poisson']) ?>% |
                            Volaille: <?= esc($item['regime']['pct_volaille']) ?>%
                        </span>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <div class="totals-box">
        <h2>Résumé du plan</h2>
        <div class="totals-content">
            <div class="total-item">
                <span class="label">Poids total à perdre/gagner:</span>
                <span class="value"><?= abs(esc($totalPoids)) ?> kg</span>
            </div>
            <div class="total-item">
                <span class="label">Durée totale:</span>
                <span class="value"><?= esc($totalTemps) ?> jours</span>
            </div>
            <div class="total-item">
                <span class="label">Coût total:</span>
                <span class="value highlight"><?= number_format($totalPrix, 0, ',', ' ') ?> Ar</span>
            </div>
        </div>
    </div>

    <div class="actions">
        <a href="/regimes/print/<?= urlencode($combinaisonJson) ?>" class="btn btn-pdf" target="_blank">
            📄 Imprimer / Exporter en PDF
        </a>
        <form method="POST" action="/regimes/souscrire" style="display:inline;">
            <?= csrf_field()?>
            <input type="hidden" name="combinaison" value="<?= esc($combinaisonJson)?>">
            <button type="submit" class="btn btn-subscribe">
                ✓ Souscrire au plan
            </button>
        </form>
    </div>
</div>
</body>
</html>

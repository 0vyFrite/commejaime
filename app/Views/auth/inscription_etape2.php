<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Inscription — Étape 2</title>
<link rel="stylesheet" href="<?= base_url('assets/css/inscription_etape2.css') ?>">
</head>
<body>
<div class="card">
    <div class="steps">
        <div class="step done"></div>
        <div class="step active"></div>
    </div>
    <h1>Informations de santé</h1>
    <p class="sub">Étape 2 sur 2 — Calcul de votre IMC</p>
    <?php if(!empty($erreurs)){ ?>
        <div class="erreur-box">
            <?php foreach($erreurs as $msg){ ?>
                <div><?= esc($msg) ?></div>
            <?php } ?>
        </div>
    <?php } ?>
    <form method="POST" action="/inscription/etape2">
        <?= csrf_field() ?>
        <div class="deux-col">
            <div class="form-group">
                <label for="taille_cm">
                    Taille <span>(cm)</span>
                </label>
                <input
                    type="number"
                    id="taille_cm"
                    name="taille_cm"
                    value="<?= esc($anciennes['taille_cm'] ?? '') ?>"
                    class="<?= !empty($erreurs['taille_cm']) ? 'err' : '' ?>"
                    placeholder="Ex : 175"
                    min="50"
                    max="250"
                    step="0.1"
                    oninput="calculerIMC()"
                    required
            >
                <?php if(!empty($erreurs['taille_cm'])){ ?>
                    <p class="msg-err">
                        <?= esc($erreurs['taille_cm']) ?>
                    </p>
                <?php } ?>
            </div>
            <div class="form-group">
                <label for="poids_kg">
                    Poids <span>(kg)</span>
                </label>
                <input
                    type="number"
                    id="poids_kg"
                    name="poids_kg"
                    value="<?= esc($anciennes['poids_kg'] ?? '') ?>"
                    class="<?= !empty($erreurs['poids_kg']) ? 'err' : '' ?>"
                    placeholder="Ex : 70"
                    min="10"
                    max="300"
                    step="0.1"
                    oninput="calculerIMC()"
                    required
            >
                <?php if(!empty($erreurs['poids_kg'])){ ?>
                    <p class="msg-err">
                        <?= esc($erreurs['poids_kg']) ?>
                    </p>
                <?php } ?>
            </div>
        </div>
        <div id="imc-box"></div>
        <div class="form-group">
            <label>Objectif</label>
            <div class="objectifs">
                <label class="obj-label">
                    <input
                        type="radio"
                        name="objectif"
                        value="reduire_poids"
                        <?= ($anciennes['objectif'] ?? '') === 'reduire_poids' ? 'checked' : '' ?>
                        required
                >
                    <div>
                        <div class="obj-titre">
                            ⬇ Réduire mon poids
                        </div>
                        <div class="obj-desc">
                            Perdre du poids grâce à un régime adapté
                        </div>
                    </div>
                </label>
                <label class="obj-label">
                    <input
                        type="radio"
                        name="objectif"
                        value="augmenter_poids"
                        <?= ($anciennes['objectif'] ?? '') === 'augmenter_poids' ? 'checked' : '' ?>
                >
                    <div>
                        <div class="obj-titre">
                            ⬆ Augmenter mon poids
                        </div>

                        <div class="obj-desc">
                            Prendre de la masse musculaire
                        </div>
                    </div>
                </label>
                <label class="obj-label">
                    <input
                        type="radio"
                        name="objectif"
                        value="imc_ideal"
                        <?= ($anciennes['objectif'] ?? '') === 'imc_ideal' ? 'checked' : '' ?>
                >
                    <div>
                        <div class="obj-titre">
                            🎯 Atteindre mon IMC idéal
                        </div>
                        <div class="obj-desc">
                            Maintenir un poids de forme optimal
                        </div>
                    </div>
                </label>
            </div>
            <?php if(!empty($erreurs['objectif'])){ ?>
                <p class="msg-err">
                    <?= esc($erreurs['objectif']) ?>
                </p>
            <?php } ?>
        </div>
        <button type="submit" class="btn">
            Créer mon compte
        </button>
    </form>
    <p class="retour">
        <a href="/inscription">
            ← Retour étape 1
        </a>
    </p>
</div>
<script>
    const categoriesIMC = <?= json_encode($categoriesIMC ?? []) ?>;
</script>
<script src="<?= base_url('assets/js/inscription_etape2.js') ?>"></script>
</body>
</html>
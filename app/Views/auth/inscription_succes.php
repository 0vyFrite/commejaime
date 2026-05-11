<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Inscription réussie</title>
<link rel="stylesheet" href="<?= base_url('assets/css/inscription_succes.css') ?>">
</head>
<body>
<div class="card">
    <div class="icone">
        🎉
    </div>
    <h1>
        Compte créé avec succès !
    </h1>
    <p class="message">
        Bienvenue
        <strong><?= esc($nom) ?></strong>
        !<br>
        Votre profil a été enregistré.
        Voici votre IMC :
    </p>
    <div class="imc-box">
        <div class="imc-valeur">
            <?= esc($imc) ?>
        </div>
        <div class="imc-label">
            Indice de Masse Corporelle
        </div>
        <div class="imc-texte">
            <?= esc($imcTexte) ?>
        </div>
    </div>
    <a href="/dashboard" class="btn">
        Accéder à mon espace →
    </a>
</div>

</body>
</html>
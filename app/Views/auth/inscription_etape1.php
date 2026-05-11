<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Inscription — Étape 1</title>
<link rel="stylesheet" href="<?= base_url('assets/css/inscription_etape1.css')?>">
</head>
<body>
<div class="card">
    <div class="steps">
        <div class="step active"></div>
        <div class="step"></div>
    </div>
    <h1>Créer un compte</h1>
    <p class="sub">
        Étape 1 sur 2 — Informations personnelles
    </p>
    <?php if(!empty($erreurs['global'])){?>
        <div class="erreur-box">
            <?= esc($erreurs['global'])?>
        </div>
    <?php }?>
    <?php if(session()->getFlashdata('erreur')){?>
        <div class="erreur-box">
            <?= esc(session()->getFlashdata('erreur'))?>
        </div>
    <?php }?>
    <form method="POST" action="/inscription">
        <?= csrf_field()?>
        <!-- NOM -->
        <div class="form-group">
            <label for="nom">Nom</label>
            <input
                type="text"
                id="nom"
                name="nom"
                value="<?= esc($anciennes['nom'] ??'')?>"
                class="<?= !empty($erreurs['nom']) ? 'err' :''?>"
                placeholder="Ex : Rakoto"
                required            >
            <?php if(!empty($erreurs['nom'])){?>
                <p class="msg-err">
                    <?= esc($erreurs['nom'])?>
                </p>
            <?php }?>
        </div>
        <!-- EMAIL -->
        <div class="form-group">
            <label for="email">Email</label>
            <input
                type="email"
                id="email"
                name="email"
                value="<?= esc($anciennes['email'] ??'')?>"
                class="<?= !empty($erreurs['email']) ? 'err' :''?>"
                placeholder="exemple@mail.com"
                required            >
            <?php if(!empty($erreurs['email'])){?>
                <p class="msg-err">
                    <?= esc($erreurs['email'])?>
                </p>
            <?php }?>
        </div>
        <!-- GENRE -->
        <div class="form-group">
            <label for="genre">Genre</label>
            <select
                id="genre"
                name="genre"
                class="<?= !empty($erreurs['genre']) ? 'err' :''?>"
                required            >
                <option value="">-- Sélectionner --</option>
                <option
                    value="homme"
                    <?= ($anciennes['genre'] ??'') == 'homme' ? 'selected' :''?>                >
                    Homme
                </option>
                <option
                    value="femme"
                    <?= ($anciennes['genre'] ??'') == 'femme' ? 'selected' :''?>                >
                    Femme
                </option>
                <option
                    value="autre"
                    <?= ($anciennes['genre'] ??'') == 'autre' ? 'selected' :''?>                >
                    Autre
                </option>
            </select>
            <?php if(!empty($erreurs['genre'])){?>
                <p class="msg-err">
                    <?= esc($erreurs['genre'])?>
                </p>
            <?php }?>
        </div>
        <!-- PASSWORD -->
        <div class="form-group">

            <label for="mot_de_passe">
                Mot de passe
            </label>
            <input
                type="password"
                id="mot_de_passe"
                name="mot_de_passe"
                class="<?= !empty($erreurs['mot_de_passe']) ? 'err' :''?>"
                placeholder="Minimum 6 caractères"
                required>
            <?php if(!empty($erreurs['mot_de_passe'])){?>
                <p class="msg-err">
                    <?= esc($erreurs['mot_de_passe'])?>
                </p>
            <?php }?>
        </div>
        <!-- CONFIRMATION -->
        <div class="form-group">
            <label for="confirmation">
                Confirmer le mot de passe
            </label>
            <input
                type="password"
                id="confirmation"
                name="confirmation"
                class="<?= !empty($erreurs['confirmation']) ? 'err' :''?>"
                placeholder="Retaper le mot de passe"
                required>
            <?php if(!empty($erreurs['confirmation'])){?>
                <p class="msg-err">
                    <?= esc($erreurs['confirmation'])?>
                </p>
            <?php }?>
        </div>
        <button type="submit" class="btn">
            Continuer →
        </button>
    </form>
    <p class="lien">
        Déjà un compte ?
        <a href="/login">Se connecter</a>
    </p>
</div>
</body>
</html>
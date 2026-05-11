<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion</title>
    <link rel="stylesheet" href="<?= base_url('assets/css/login.css') ?>">
</head>
<body>
    <div class="card">
        <div class="logo">🥗</div>
        <h1>Bienvenue</h1>
        <p class="sous-titre">
            Connectez-vous à votre compte
        </p>
        <?php if (session()->getFlashdata('erreur')) { ?>
            <div class="alerte-erreur">
                <?= esc(session()->getFlashdata('erreur')) ?>
            </div>
        <?php } ?>
        <?php if (session()->getFlashdata('succes')) { ?>
            <div class="alerte-succes">
                <?= esc(session()->getFlashdata('succes')) ?>
            </div>
        <?php } ?>
        <?php if (!empty($erreurs['global'])) { ?>
            <div class="alerte-erreur">
                <?= esc($erreurs['global']) ?>
            </div>
        <?php } ?>
        <form method="POST" action="/login">
            <?= csrf_field() ?>
            <div class="form-group">
                <label for="email">
                    Adresse email
                </label>
                <input type="email" id="email" name="email" value="<?= esc($anciennes['email'] ?? '') ?>"
                    class="<?= !empty($erreurs['email']) ? 'erreur' : '' ?>" placeholder="exemple@mail.com"
                    autocomplete="email" required>
                <?php if (!empty($erreurs['email'])) { ?>
                    <p class="msg-erreur">
                        <?= esc($erreurs['email']) ?>
                    </p>
                <?php } ?>
            </div>
            <div class="form-group">
                <label for="mot_de_passe">
                    Mot de passe
                </label>
                <input type="password" id="mot_de_passe" name="mot_de_passe"
                    class="<?= !empty($erreurs['mot_de_passe']) ? 'erreur' : '' ?>" placeholder="Votre mot de passe"
                    autocomplete="current-password" required>
                <?php if (!empty($erreurs['mot_de_passe'])) { ?>
                    <p class="msg-erreur">
                        <?= esc($erreurs['mot_de_passe']) ?>
                    </p>
                <?php } ?>
            </div>
            <button type="submit" class="btn">
                Se connecter
            </button>
        </form>
        <p class="lien-inscription">
            Pas encore de compte ?
            <a href="/inscription">
                S'inscrire
            </a>
        </p>
    </div>
</body>
</html>
# 📋 Guide de création des fichiers — DietApp (CodeIgniter 4)

> **Projet :** Application de gestion de régime alimentaire  
> **Stack :** PHP · CodeIgniter 4 · MySQL · Bootstrap 5  
> **Architecture :** MVC strict — Front Office / Back Office séparés

---

## ⚠️ Règle d'or avant de commencer

Toujours créer dans cet ordre : **Base de données → Config → Filtres → Models → Controllers → Views**  
Un fichier qui arrive plus tôt dans la liste ne doit jamais dépendre d'un fichier qui arrive plus tard.

---

## ÉTAPE 1 — Base de données

| # | Fichier | Rôle |
|---|---------|------|
| 1 | `sql/diet_app.sql` | Script SQL complet : création des tables, insertion des données de test (5 utilisateurs, 5 régimes, 5 activités, 15 codes, paramètres, abonnements). **À exécuter en premier dans phpMyAdmin ou MySQL CLI.** |

### Tables créées par ce script

```
users               → Comptes utilisateurs (nom, email, taille, poids, IMC, objectif, solde, rôle)
regimes             → Régimes alimentaires (nom, prix, durée, composition viande/poisson/volaille)
activites           → Activités sportives (nom, calories/h, intensité, objectif cible)
codes_portefeuille  → Codes de recharge du porte-monnaie
abonnements         → Souscriptions d'un utilisateur à un régime
parametres          → Paramètres de l'application (clé → valeur)
```

---

## ÉTAPE 2 — Configuration CI4

| # | Fichier | Rôle |
|---|---------|------|
| 2 | `app/Config/Routes.php` | Déclare **toutes les URLs** de l'application. Associe chaque URL à un Controller et une méthode. Applique les filtres `auth` sur les routes protégées. **Sans ce fichier, aucune page n'est accessible.** |

### Ce que Routes.php définit

```
/                          → AuthController::login   (page d'accueil = login)
/login  /logout            → AuthController
/register/step1  /step2    → AuthController (inscription en 2 étapes)
/dashboard                 → Front\DashboardController
/profil                    → Front\ProfilController
/objectif                  → Front\ObjectifController
/regimes                   → Front\RegimeController
/activites                 → Front\ActiviteController
/portefeuille              → Front\PortefeuilleController
/gold/activer              → Front\GoldController
/export/pdf                → Front\ExportController
/admin/*                   → Controllers\Admin\*   (back office)
```

---

## ÉTAPE 3 — Filtres (Middleware)

Les filtres s'exécutent **avant** chaque requête. Ils doivent exister avant que les routes puissent les référencer.

| # | Fichier | Rôle |
|---|---------|------|
| 3 | `app/Filters/AuthFilter.php` | Vérifie qu'un utilisateur est connecté (`session user_id`). Si non → redirige vers `/login`. Vérifie aussi que les routes `/admin/*` ne sont accessibles qu'aux admins. |
| 4 | `app/Filters/RoleFilter.php` | Vérifie le rôle de l'utilisateur connecté. Usage : `['filter' => 'role:admin']` dans les routes. Redirige vers `/dashboard` si le rôle ne correspond pas. |

> **Comment enregistrer les filtres :** Dans `app/Config/Filters.php` (fichier natif CI4), ajouter dans le tableau `$aliases` :
> ```php
> 'auth' => \App\Filters\AuthFilter::class,
> 'role' => \App\Filters\RoleFilter::class,
> ```

---

## ÉTAPE 4 — Models (accès base de données)

Les Models sont créés **avant** les Controllers car les Controllers les instancient. Chaque Model correspond à une table. **Aucune requête SQL ne doit se trouver ailleurs que dans les Models.**

| # | Fichier | Table associée | Rôle |
|---|---------|----------------|------|
| 5 | `app/Models/UserModel.php` | `users` | Gestion des utilisateurs. Hash automatique du mot de passe (beforeInsert/beforeUpdate). Méthodes : `findByEmail()`, `calculerIMC()`, `rechargerSolde()`, `debiterSolde()`, `getStats()`, `getAllWithAbonnement()`. |
| 6 | `app/Models/RegimeModel.php` | `regimes` | Gestion des régimes alimentaires. Méthodes : `getByObjectif()` pour filtrer par objectif utilisateur, `calculerPrix()` avec réduction Gold (-15%), `getAllActifs()`, `getStats()`. |
| 7 | `app/Models/ActiviteModel.php` | `activites` | Gestion des activités sportives. Méthode principale : `getByObjectif()` pour suggestions selon l'objectif. |
| 8 | `app/Models/CodePortefeuilleModel.php` | `codes_portefeuille` | Gestion des codes de recharge. Méthode clé : `utiliserCode()` qui valide, marque le code comme utilisé et retourne le montant. |
| 9 | `app/Models/AbonnementModel.php` | `abonnements` | Gestion des souscriptions. Méthodes : `getAbonnementActif()` (avec jointure régime), `getHistorique()`, `getStats()`. |
| 10 | `app/Models/ParametreModel.php` | `parametres` | Paramètres clé/valeur de l'app. Méthodes : `get(cle)`, `set(cle, valeur)`, `getAllAsArray()`. |

---

## ÉTAPE 5 — Controllers Auth (inscription / connexion)

| # | Fichier | Rôle |
|---|---------|------|
| 11 | `app/Controllers/Auth/AuthController.php` | Gère l'authentification complète. Méthodes : `login()` / `doLogin()` (vérification email+password, création session), `logout()` (destroy session), `registerStep1()` / `doRegisterStep1()` (formulaire infos perso, stockage session temporaire), `registerStep2()` / `doRegisterStep2()` (infos santé, calcul IMC, création compte, connexion auto). |

---

## ÉTAPE 6 — Controllers Front Office

Ces controllers gèrent les pages accessibles aux utilisateurs connectés. Ils utilisent le filtre `auth`.

| # | Fichier | URL gérée | Rôle |
|---|---------|-----------|------|
| 12 | `app/Controllers/Front/DashboardController.php` | `/dashboard` | Page principale. Charge le profil, l'abonnement actif, l'historique et calcule la catégorie IMC (insuffisance/normal/surpoids/obésité). |
| 13 | `app/Controllers/Front/ProfilController.php` | `/profil` | Affichage et mise à jour du profil. Recalcule l'IMC à chaque modification taille/poids. |
| 14 | `app/Controllers/Front/ObjectifController.php` | `/objectif` | Affichage et sauvegarde de l'objectif utilisateur (augmenter / réduire / idéal). Redirige vers les régimes après validation. |
| 15 | `app/Controllers/Front/RegimeController.php` | `/regimes` | Liste les régimes filtrés par objectif. Applique la réduction Gold (-15%). Gère la souscription : vérifie le solde, débite le porte-monnaie, crée l'abonnement. |
| 16 | `app/Controllers/Front/ActiviteController.php` | `/activites` | Affiche les activités sportives filtrées selon l'objectif de l'utilisateur. |
| 17 | `app/Controllers/Front/PortefeuilleController.php` | `/portefeuille` | Affiche le solde. Gère la recharge par code via `CodePortefeuilleModel::utiliserCode()`. |
| 18 | `app/Controllers/Front/GoldController.php` | `/gold/activer` | Active l'option Gold (9.99€ débité du solde). Met à jour `is_gold = 1` et la session. |
| 19 | `app/Controllers/Front/ExportController.php` | `/export/pdf` | Prépare toutes les données du bilan (profil, régime actif, historique, activités) et les passe à la vue `export_pdf.php`. L'impression/PDF est gérée côté navigateur. |

---

## ÉTAPE 7 — Controllers Back Office (Admin)

Ces controllers gèrent les pages du back office. Accessibles uniquement avec `role = admin`.

| # | Fichier | URL gérée | Rôle |
|---|---------|-----------|------|
| 20 | `app/Controllers/Admin/AdminController.php` | `/admin` | Dashboard admin avec statistiques (nb utilisateurs, Gold, abonnements, revenus) et données pour graphique Chart.js des abonnements par régime. |
| 21 | `app/Controllers/Admin/RegimeAdminController.php` | `/admin/regimes` | CRUD complet des régimes. Suppression logique (actif = 0). Validation des champs métier (pourcentages, prix, durée, objectif). |
| 22 | `app/Controllers/Admin/ActiviteAdminController.php` | `/admin/activites` | CRUD complet des activités sportives. Validation intensité (faible/modere/eleve) et objectif. |
| 23 | `app/Controllers/Admin/CodeAdminController.php` | `/admin/codes` | Listing des codes avec statut utilisé/disponible. Création avec génération automatique du code (`CODE-XXXX-XXXX`). Suppression uniquement si code non utilisé. |
| 24 | `app/Controllers/Admin/ParametreAdminController.php` | `/admin/parametres` | Affichage et mise à jour des paramètres (reduction Gold %, prix Gold, IMC seuils, etc.) via `ParametreModel::set()`. |
| 25 | `app/Controllers/Admin/UserAdminController.php` | `/admin/users` | Listing des utilisateurs avec leur régime actif (jointure). Fiche détail avec historique des abonnements. |

---

## ÉTAPE 8 — Layouts (gabarits HTML)

Les layouts contiennent le HTML commun (sidebar, topbar, alertes flash). Ils sont chargés **avant** la vue de contenu par chaque Controller via `view('layouts/front', $data) . view('front/xxx', $data)`.

| # | Fichier | Rôle |
|---|---------|------|
| 26 | `app/Views/layouts/front.php` | Layout du Front Office. Contient : balises HTML head, sidebar verte avec navigation utilisateur (dashboard/profil/objectif/régimes/activités/portefeuille/export), topbar avec bouton Gold, bloc alertes flash (success/error/info/errors). |
| 27 | `app/Views/layouts/admin.php` | Layout du Back Office. Contient : sidebar bleue foncée avec navigation admin (dashboard/régimes/activités/codes/utilisateurs/paramètres), topbar, bloc alertes flash. |

---

## ÉTAPE 9 — Views Auth

| # | Fichier | Rôle |
|---|---------|------|
| 28 | `app/Views/auth/login.php` | Page de connexion standalone (sans layout). Design split : côté gauche décoratif vert, côté droit formulaire email/password. Affiche les comptes de démo. |
| 29 | `app/Views/auth/register_step1.php` | Formulaire inscription étape 1 : nom, email, password, confirmation, genre. Barre de progression (étape 1/2). Données stockées en session jusqu'à l'étape 2. |
| 30 | `app/Views/auth/register_step2.php` | Formulaire inscription étape 2 : taille (cm), poids (kg). Calcul IMC en temps réel via JavaScript au fil de la saisie, avec catégorie colorée. |

---

## ÉTAPE 10 — Views Front Office

| # | Fichier | Rôle |
|---|---------|------|
| 31 | `app/Views/front/dashboard.php` | Page principale utilisateur. 4 cards résumé (IMC coloré, taille/poids, objectif, solde). Section régime actif avec barres de progression composition (viande/poisson/volaille). Historique des abonnements en tableau. |
| 32 | `app/Views/front/profil.php` | Formulaire de mise à jour nom/taille/poids. Recalcul IMC en temps réel (JavaScript). Affiche l'email et le genre en lecture seule. |
| 33 | `app/Views/front/objectif.php` | Sélection de l'objectif via 3 boutons radio stylisés (réduire / augmenter / idéal). Rappel IMC actuel. |
| 34 | `app/Views/front/regimes.php` | Grille des régimes filtrés. Chaque carte affiche : description, barres de progression composition, durée, variation de poids, prix (barré si Gold avec prix réduit), bouton souscrire avec confirmation. |
| 35 | `app/Views/front/activites.php` | Grille des activités filtrées. Chaque carte affiche : nom, badge intensité coloré (vert/orange/rouge), description, calories/heure. |
| 36 | `app/Views/front/portefeuille.php` | Affichage solde + badge Gold. Formulaire de recharge par code. Bouton activation Gold si non activé. Codes de démo affichés pour faciliter les tests. |
| 37 | `app/Views/front/export_pdf.php` | Vue dédiée à l'impression. Sans layout. Contient : en-tête DietApp, données santé (IMC + catégorie), régime actif, activités recommandées, historique. Bouton "Imprimer / Sauvegarder en PDF" (window.print()). |

---

## Récapitulatif visuel — Dépendances

```
sql/diet_app.sql
        │
        ▼
app/Config/Routes.php
        │
        ├──▶ app/Filters/AuthFilter.php
        │    app/Filters/RoleFilter.php
        │
        ├──▶ app/Models/UserModel.php
        │    app/Models/RegimeModel.php
        │    app/Models/ActiviteModel.php
        │    app/Models/CodePortefeuilleModel.php
        │    app/Models/AbonnementModel.php
        │    app/Models/ParametreModel.php
        │
        ├──▶ app/Controllers/Auth/AuthController.php
        │
        ├──▶ app/Controllers/Front/DashboardController.php
        │    app/Controllers/Front/ProfilController.php
        │    app/Controllers/Front/ObjectifController.php
        │    app/Controllers/Front/RegimeController.php
        │    app/Controllers/Front/ActiviteController.php
        │    app/Controllers/Front/PortefeuilleController.php
        │    app/Controllers/Front/GoldController.php
        │    app/Controllers/Front/ExportController.php
        │
        └──▶ app/Controllers/Admin/AdminController.php
             app/Controllers/Admin/RegimeAdminController.php
             app/Controllers/Admin/ActiviteAdminController.php
             app/Controllers/Admin/CodeAdminController.php
             app/Controllers/Admin/ParametreAdminController.php
             app/Controllers/Admin/UserAdminController.php
                    │
                    ▼
        app/Views/layouts/front.php      (chargé par tous les controllers Front)
        app/Views/layouts/admin.php      (chargé par tous les controllers Admin)
                    │
                    ▼
        app/Views/auth/login.php
        app/Views/auth/register_step1.php
        app/Views/auth/register_step2.php
        app/Views/front/dashboard.php
        app/Views/front/profil.php
        app/Views/front/objectif.php
        app/Views/front/regimes.php
        app/Views/front/activites.php
        app/Views/front/portefeuille.php
        app/Views/front/export_pdf.php
```

---

## 🚀 Installation rapide

```bash
# 1. Installer CodeIgniter 4
composer create-project codeigniter4/appstarter diet_app
cd diet_app

# 2. Configurer la base de données dans app/Config/Database.php
#    ou dans le fichier .env :
database.default.hostname = localhost
database.default.database = diet_app
database.default.username = root
database.default.password = 
database.default.DBDriver = MySQLi

# 3. Importer le script SQL
mysql -u root -p diet_app < sql/diet_app.sql

# 4. Copier les fichiers du projet dans les bons dossiers

# 5. Enregistrer les filtres dans app/Config/Filters.php
#    Ajouter dans $aliases :
#    'auth' => \App\Filters\AuthFilter::class,
#    'role' => \App\Filters\RoleFilter::class,

# 6. Lancer le serveur de développement
php spark serve

# 7. Ouvrir http://localhost:8080
#    Admin : admin@dietapp.com  / password
#    User  : alice@test.com     / password
```

---

## 📁 Arborescence finale

```
diet_app/
├── sql/
│   └── diet_app.sql                          (1)  ← Commencer ici
│
└── app/
    ├── Config/
    │   └── Routes.php                         (2)
    │
    ├── Filters/
    │   ├── AuthFilter.php                     (3)
    │   └── RoleFilter.php                     (4)
    │
    ├── Models/
    │   ├── UserModel.php                      (5)
    │   ├── RegimeModel.php                    (6)
    │   ├── ActiviteModel.php                  (7)
    │   ├── CodePortefeuilleModel.php          (8)
    │   ├── AbonnementModel.php                (9)
    │   └── ParametreModel.php                 (10)
    │
    ├── Controllers/
    │   ├── Auth/
    │   │   └── AuthController.php             (11)
    │   ├── Front/
    │   │   ├── DashboardController.php        (12)
    │   │   ├── ProfilController.php           (13)
    │   │   ├── ObjectifController.php         (14)
    │   │   ├── RegimeController.php           (15)
    │   │   ├── ActiviteController.php         (16)
    │   │   ├── PortefeuilleController.php     (17)
    │   │   ├── GoldController.php             (18)
    │   │   └── ExportController.php           (19)
    │   └── Admin/
    │       ├── AdminController.php            (20)
    │       ├── RegimeAdminController.php      (21)
    │       ├── ActiviteAdminController.php    (22)
    │       ├── CodeAdminController.php        (23)
    │       ├── ParametreAdminController.php   (24)
    │       └── UserAdminController.php        (25)
    │
    └── Views/
        ├── layouts/
        │   ├── front.php                      (26)
        │   └── admin.php                      (27)
        ├── auth/
        │   ├── login.php                      (28)
        │   ├── register_step1.php             (29)
        │   └── register_step2.php             (30)
        └── front/
            ├── dashboard.php                  (31)
            ├── profil.php                     (32)
            ├── objectif.php                   (33)
            ├── regimes.php                    (34)
            ├── activites.php                  (35)
            ├── portefeuille.php               (36)
            └── export_pdf.php                 (37)
```

> **Total : 37 fichiers** à créer dans l'ordre indiqué.

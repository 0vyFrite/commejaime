<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Plan de régime</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            color: #333;
        }
        .header {
            text-align: center;
            border-bottom: 3px solid #4CAF50;
            padding-bottom: 20px;
            margin-bottom: 20px;
        }
        .header h1 {
            color: #2c3e50;
            margin: 0 0 10px 0;
        }
        .user-info {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        .user-info p {
            margin: 5px 0;
        }
        .regime-details {
            background: #fff;
            border: 2px solid #4CAF50;
            padding: 20px;
            margin-bottom: 20px;
            border-radius: 8px;
        }
        .regime-title {
            font-size: 24px;
            color: #2c3e50;
            margin-bottom: 10px;
        }
        .regime-description {
            color: #666;
            font-size: 14px;
            margin-bottom: 15px;
        }
        .percentage-badge {
            display: inline-block;
            background: #4CAF50;
            color: white;
            padding: 8px 16px;
            border-radius: 20px;
            font-weight: bold;
            margin-bottom: 15px;
        }
        .details-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .details-table th,
        .details-table td {
            border: 1px solid #ddd;
            padding: 12px;
            text-align: left;
        }
        .details-table th {
            background: #4CAF50;
            color: white;
            font-weight: bold;
        }
        .details-table tr:nth-child(even) {
            background: #f8f9fa;
        }
        .composition-bars {
            margin: 15px 0;
        }
        .bar-item {
            margin-bottom: 12px;
        }
        .bar-label {
            font-size: 14px;
            font-weight: bold;
            color: #333;
            margin-bottom: 4px;
        }
        .progress-bar {
            background: #e0e0e0;
            height: 25px;
            border-radius: 4px;
            overflow: hidden;
            position: relative;
        }
        .progress-fill {
            background: linear-gradient(90deg, #4CAF50, #45a049);
            height: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: bold;
            font-size: 12px;
        }
        .footer {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 2px solid #ddd;
            text-align: center;
            font-size: 12px;
            color: #999;
        }
        .page-break {
            page-break-after: always;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>🥗 Plan de Régime Personnalisé</h1>
        <p>Document généré pour <?= esc($user['nom']) ?></p>
    </div>

    <div class="user-info">
        <p><strong>Utilisateur:</strong> <?= esc($user['nom']) ?></p>
        <p><strong>Email:</strong> <?= esc($user['email']) ?></p>
        <p><strong>Date:</strong> <?= date('d/m/Y') ?></p>
        <p><strong>Taille:</strong> <?= esc($profil['taille_cm']) ?> cm - <strong>Poids:</strong> <?= esc($profil['poids_kg']) ?> kg - <strong>IMC:</strong> <?= esc($profil['imc']) ?></p>
    </div>

    <div class="regime-details">
        <div class="regime-title"><?= esc($regime['nom']) ?></div>
        
        <div class="percentage-badge">
            <?= esc($pourcentage) ?>% du traitement
        </div>

        <div class="regime-description">
            <?= esc($regime['description']) ?>
        </div>

        <table class="details-table">
            <tr>
                <th>Paramètre</th>
                <th>Valeur</th>
            </tr>
            <tr>
                <td>Variation de poids (régime base)</td>
                <td><?= esc($regime['variation_poids_kg']) ?> kg</td>
            </tr>
            <tr>
                <td>Durée du régime (base)</td>
                <td><?= esc($regime['duree_jours']) ?> jours</td>
            </tr>
            <tr>
                <td>Durée calculée pour vous</td>
                <td><strong><?= esc($joursNecessaires) ?> jours</strong></td>
            </tr>
            <tr>
                <td>Prix (prix unitaire)</td>
                <td><?= number_format($regime['prix'], 0, ',', ' ') ?> Ar</td>
            </tr>
        </table>

        <div>
            <h3>Composition du régime</h3>
            <div class="composition-bars">
                <div class="bar-item">
                    <div class="bar-label">Viande: <?= esc($regime['pct_viande']) ?>%</div>
                    <div class="progress-bar">
                        <div class="progress-fill" style="width: <?= esc($regime['pct_viande']) ?>%">
                            <?= esc($regime['pct_viande']) ?>%
                        </div>
                    </div>
                </div>
                <div class="bar-item">
                    <div class="bar-label">Poisson: <?= esc($regime['pct_poisson']) ?>%</div>
                    <div class="progress-bar">
                        <div class="progress-fill" style="width: <?= esc($regime['pct_poisson']) ?>%">
                            <?= esc($regime['pct_poisson']) ?>%
                        </div>
                    </div>
                </div>
                <div class="bar-item">
                    <div class="bar-label">Volaille: <?= esc($regime['pct_volaille']) ?>%</div>
                    <div class="progress-bar">
                        <div class="progress-fill" style="width: <?= esc($regime['pct_volaille']) ?>%">
                            <?= esc($regime['pct_volaille']) ?>%
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="footer">
        <p>Document généré par RégimeSanté - Tous droits réservés</p>
        <p>Pour plus d'informations, consultez votre profil RégimeSanté</p>
    </div>
</body>
</html>

<!-- CETTE VUE EST OBSOLÈTE - Le système de régimes a été refactorisé -->
<!-- Utilisez la nouvelle vue export-pdf.php pour exporter les régimes -->

<?php
throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
?>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Plan régimes - <?= esc($user['nom']) ?></title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: #333;
            line-height: 1.6;
            padding: 20px;
            background: #f5f7fa;
        }

        .print-container {
            background: white;
            max-width: 21cm;
            margin: 0 auto;
            padding: 20px;
        }

        .print-header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 3px solid #4CAF50;
        }

        .print-header h1 {
            color: #2c3e50;
            margin-bottom: 5px;
            font-size: 28px;
        }

        .print-header p {
            color: #7f8c8d;
            font-size: 14px;
        }

        .user-info {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 30px;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 20px;
        }

        .user-info-item {
            text-align: left;
        }

        .user-info-item label {
            display: block;
            font-weight: bold;
            color: #4CAF50;
            font-size: 12px;
            margin-bottom: 3px;
        }

        .user-info-item span {
            display: block;
            color: #2c3e50;
            font-size: 16px;
        }

        .regimes-section h2 {
            color: #2c3e50;
            margin: 30px 0 15px 0;
            font-size: 20px;
            border-bottom: 2px solid #4CAF50;
            padding-bottom: 10px;
            page-break-after: avoid;
        }

        .regime-item {
            border: 1px solid #ddd;
            border-left: 4px solid #4CAF50;
            padding: 15px;
            margin-bottom: 15px;
            background: white;
            page-break-inside: avoid;
        }

        .regime-item h3 {
            color: #2c3e50;
            margin-bottom: 10px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 16px;
        }

        .repetition-badge {
            background: #4CAF50;
            color: white;
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: bold;
        }

        .regime-info {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 10px;
            font-size: 13px;
        }

        .info-field {
            display: flex;
            justify-content: space-between;
            padding: 8px;
            background: #f8f9fa;
            border-radius: 3px;
        }

        .info-field .label {
            font-weight: 600;
            color: #555;
        }

        .info-field .value {
            color: #2c3e50;
            font-weight: 500;
        }

        .totals-section {
            background: #e8f5e9;
            padding: 20px;
            border-radius: 5px;
            margin: 40px 0;
            page-break-inside: avoid;
        }

        .totals-section h2 {
            color: #2c3e50;
            margin: 0 0 15px 0;
            font-size: 18px;
            border-bottom: 2px solid #4CAF50;
            padding-bottom: 10px;
        }

        .total-items {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 15px;
        }

        .total-item {
            background: white;
            padding: 15px;
            border-radius: 5px;
            text-align: center;
            border: 1px solid #c8e6c9;
        }

        .total-item label {
            display: block;
            color: #555;
            font-size: 12px;
            margin-bottom: 5px;
            font-weight: 600;
        }

        .total-item .value {
            display: block;
            font-size: 24px;
            color: #4CAF50;
            font-weight: bold;
        }

        .total-item .value.price {
            color: #e74c3c;
        }

        .footer {
            text-align: center;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #ddd;
            color: #999;
            font-size: 11px;
        }

        .print-actions {
            text-align: center;
            margin-bottom: 20px;
            padding: 10px;
            background: #f8f9fa;
            border-radius: 5px;
        }

        .print-btn {
            display: inline-block;
            padding: 10px 30px;
            background: linear-gradient(135deg, #3498db, #2980b9);
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
            font-weight: bold;
            margin: 0 10px;
            transition: all 0.3s ease;
        }

        .print-btn:hover {
            background: linear-gradient(135deg, #2980b9, #1f618d);
            transform: translateY(-2px);
        }

        @media print {
            body {
                background: white;
                padding: 0;
            }

            .print-container {
                max-width: 100%;
                padding: 0;
                margin: 0;
                box-shadow: none;
            }

            .print-actions {
                display: none;
            }

            .regimes-section h2 {
                page-break-after: avoid;
            }

            .regime-item {
                page-break-inside: avoid;
            }

            .totals-section {
                page-break-inside: avoid;
            }
        }
    </style>
</head>
<body>
    <div class="print-container">
        <div class="print-actions">
            <button class="print-btn" onclick="window.print()">
                🖨️ Imprimer / Exporter en PDF
            </button>
        </div>

        <div class="print-header">
            <h1>🥗 RégimeSanté</h1>
            <p>Plan régimes personnalisé</p>
        </div>

        <div class="user-info">
            <div class="user-info-item">
                <label>Utilisateur</label>
                <span><?= esc($user['nom']) ?></span>
            </div>
            <div class="user-info-item">
                <label>IMC Actuel</label>
                <span><?= number_format($profil['imc'], 2, ',', ' ') ?></span>
            </div>
            <div class="user-info-item">
                <label>Poids Actuel</label>
                <span><?= number_format($profil['poids_kg'], 1, ',', ' ') ?> kg</span>
            </div>
            <div class="user-info-item">
                <label>Taille</label>
                <span><?= number_format($profil['taille_cm'], 0) ?> cm</span>
            </div>
        </div>

        <div class="regimes-section">
            <h2>📋 Détails des régimes</h2>
            
            <?php foreach($details as $item): ?>
                <div class="regime-item">
                    <h3>
                        <?= esc($item['regime']['nom']) ?>
                        <span class="repetition-badge">×<?= esc($item['repetitions']) ?></span>
                    </h3>
                    <div class="regime-info">
                        <div class="info-field">
                            <span class="label">Description:</span>
                            <span class="value"><?= esc($item['regime']['description'] ?? '—') ?></span>
                        </div>
                        <div class="info-field">
                            <span class="label">Variation:</span>
                            <span class="value"><?= esc($item['poids_total']) ?> kg</span>
                        </div>
                        <div class="info-field">
                            <span class="label">Durée:</span>
                            <span class="value"><?= esc($item['temps_total']) ?> jours</span>
                        </div>
                        <div class="info-field">
                            <span class="label">Prix:</span>
                            <span class="value"><?= number_format($item['prix_total'], 0, ',', ' ') ?> Ar</span>
                        </div>
                        <div class="info-field" style="grid-column: 1/-1;">
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

        <div class="totals-section">
            <h2>📊 Résumé du plan</h2>
            <div class="total-items">
                <div class="total-item">
                    <label>Poids à perdre/gagner</label>
                    <div class="value"><?= abs(esc($totalPoids)) ?> kg</div>
                </div>
                <div class="total-item">
                    <label>Durée totale</label>
                    <div class="value"><?= esc($totalTemps) ?> jours</div>
                </div>
                <div class="total-item">
                    <label>Coût total</label>
                    <div class="value price"><?= number_format($totalPrix, 0, ',', ' ') ?> Ar</div>
                </div>
            </div>
        </div>

        <div class="footer">
            <p>Généré le <?= date('d/m/Y à H:i') ?> par RégimeSanté</p>
            <p>Ce document est confidentiel et destiné à un usage personnel</p>
        </div>
    </div>
</body>
</html>

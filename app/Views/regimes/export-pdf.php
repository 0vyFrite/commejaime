<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Plan de régime - <?= esc($regime['nom']) ?></title>
    <!-- html2pdf library for client-side PDF generation -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            color: #333;
            background: #f5f5f5;
        }
        .no-print {
            display: block;
            background: white;
            padding: 15px;
            margin-bottom: 20px;
            text-align: center;
            border-bottom: 2px solid #ddd;
        }
        .no-print button {
            background: #4CAF50;
            color: white;
            border: none;
            padding: 10px 20px;
            font-size: 16px;
            border-radius: 5px;
            cursor: pointer;
            transition: background 0.3s;
            margin: 0 5px;
        }
        .no-print button:hover {
            background: #45a049;
        }
        .no-print button:disabled {
            background: #ccc;
            cursor: not-allowed;
        }
        .document {
            background: white;
            margin: 20px auto;
            max-width: 800px;
            padding: 40px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
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
        .activite-details {
            margin-top: 5%;
            background: #fff;
            border: 2px solid #2196F3;
            padding: 20px;
            margin-bottom: 20px;
            border-radius: 8px;
        }
        .activite-title {
            font-size: 24px;
            color: #1976D2;
            margin-bottom: 10px;
        }
        .activite-description {
            color: #666;
            font-size: 14px;
            margin-bottom: 15px;
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
            page-break-before: always;
        }
        
        /* Styles d'impression */
        @media print {
            .no-print {
                display: none !important;
            }
            body {
                background: white;
            }
            .document {
                max-width: 100%;
                margin: 0;
                padding: 0;
                box-shadow: none;
            }
        }
    </style>
</head>
<body>
    <div class="no-print">
        <button onclick="exportPDF()">📥 Télécharger en PDF</button>
    </div>

    <div class="document" id="pdf-content">
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

            <div class="regime-description">
                <?= esc($regime['description']) ?>
            </div>

            <table class="details-table">
                <tr>
                    <th>Paramètre</th>
                    <th>Valeur</th>
                </tr>
                <tr>
                    <td>Variation de poids</td>
                    <td><?= esc($totalPoids) ?> kg</td>
                </tr>
                <tr>
                    <td>Durée</td>
                    <td><strong><?= esc($totalDuree) ?> jours</strong></td>
                </tr>
                <tr>
                    <td>Prix</td>
                    <td><?= number_format($totalPrix, 0, ',', ' ') ?> Ar</td>
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

        <div class="page-break"></div>

        <div class="activite-details">
            <div class="activite-title">💪 <?= esc($activite['nom']) ?></div>

            <div class="activite-description">
                <?= esc($activite['description']) ?>
            </div>

            <table class="details-table">
                <tr>
                    <th>Paramètre</th>
                    <th>Valeur</th>
                </tr>
                <tr>
                    <td>Variation de poids (activité)</td>
                    <td><?= esc($activite['variation_poids_kg']) ?> kg</td>
                </tr>
                <tr>
                    <td>Fréquence</td>
                    <td><?= esc($activite['frequence_semaine']) ?> fois par semaine</td>
                </tr>
                <tr>
                    <td>Durée par session</td>
                    <td><?= esc($activite['duree_minutes']) ?> minutes</td>
                </tr>
            </table>
        </div>

        <div class="footer">
            <p>Document généré par RégimeSanté - Tous droits réservés</p>
            <p>Pour plus d'informations, consultez votre profil RégimeSanté</p>
        </div>
    </div>

    <script>
        function exportPDF() {
            const element = document.getElementById('pdf-content');
            const filename = 'regime.pdf';
            
            // Configuration pour html2pdf
            const options = {
                margin: 10,
                filename: filename,
                image: { type: 'jpeg', quality: 0.98 },
                html2canvas: { scale: 2 },
                jsPDF: { orientation: 'portrait', unit: 'mm', format: 'a4' }
            };

            // Générer et télécharger le PDF
            html2pdf().set(options).from(element).save();
        }
    </script>
</body>
</html>

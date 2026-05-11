# Refactorisation du système de régimes - 11 mai 2026

## Résumé des changements

Le système de régimes a été complètement refactorisé pour utiliser une nouvelle logique basée sur la règle de trois.

### **Avant (système obsolète)**
- Affichage de complexes combinaisons de régimes
- Calcul récursif de toutes les combinaisons possibles pour atteindre une variation de poids
- Coût computationnel élevé
- UX complexe avec plusieurs combinaisons

### **Après (nouveau système)**
- Affichage simple de **tous les régimes disponibles** adaptés au poids optimal de l'utilisateur
- Utilisation de la **règle de trois** pour calculer la durée et le pourcentage
- Chaque régime affiche son **pourcentage de traitement**
- Meilleure UX avec des détails détaillés pour chaque régime
- **Export PDF** fonctionnel pour chaque régime

## Nouvelle logique mathématique

### Formule de calcul
Pour chaque régime:
```
jours_calculés = (duree_base × poids_cible) / poids_base
jours_calculés = floor(jours_calculés)  // Arrondir à l'entier inférieur

pourcentage = (jours_calculés / duree_base) × 100
```

### Exemple
Régime considéré:
- Variation: -6 kg
- Durée: 30 jours

Si l'utilisateur doit perdre 15 kg:
- jours_calculés = (30 × 15) / 6 = 75 jours
- jours_calculés = floor(75) = 75 jours
- pourcentage = (75 / 30) × 100 = 250%

## Fichiers modifiés

### 1. **RegimeModel** (`app/Models/RegimeModel.php`)
- ✅ **Nouvelle méthode**: `getRegimesAvecPourcentage(float $variationPoids): array`
  - Retourne tous les régimes avec effet similaire
  - Calcule le pourcentage pour chacun
  - Utilise la règle de trois
- ⚠️ **Anciennes méthodes marquées @deprecated**:
  - `getRegimes()` - Obsolète
  - `getAllRegimes()` - Obsolète
  - `orderRegimes()` - Obsolète
  - `groupAllRegimes()` - Obsolète
  - `getTempsRegime()` - Obsolète
  - `calculateTotalWeightLoss()` - Obsolète
  - `calculateTotalPrice()` - Obsolète
  - `getDetailsCombinaisonRegimes()` - Obsolète

### 2. **DashboardController** (`app/Controllers/DashboardController.php`)
- ✅ Utilise maintenant `getRegimesAvecPourcentage()`
- ✅ Affiche directement les régimes au lieu des combinaisons
- ✅ Données passées à la vue: `$regimes` (au lieu de `$combinaisons`)

### 3. **RegimesController** (`app/Controllers/RegimesController.php`)
- ✅ **Nouvelle méthode**: `exportRegimePdf($regimeId, $pourcentage)`
  - Export individual regime as PDF
  - Affiche tous les détails du régime
  - Montre le pourcentage calculé
- ⚠️ Anciennes méthodes remplacées par exception 404:
  - `getAllRegimes()` - Supprimée
  - `details()` - Supprimée
  - `print()` - Supprimée
  - `exportPdf()` - Supprimée
  - `exportPdfPost()` - Supprimée

### 4. **Routes** (`app/Config/Routes.php`)
- ✅ **Nouvelle route**: `GET /regimes/export/:num/:num` → `RegimesController::exportRegimePdf`
- ❌ Routes obsolètes supprimées:
  - `/regimes/details/:any`
  - `/regimes/print/:any`
  - `/regimes/all/:any`

### 5. **Vue Dashboard** (`app/Views/dashboard/index.php`)
- ✅ Section régimes complètement refactorisée
- ✅ Affichage des détails de chaque régime:
  - Nom et description
  - Badge de pourcentage
  - Composition (viandes, poissons, volaille)
  - Variations et durées
  - Prix
- ✅ Bouton "📥 Exporter en PDF" pour chaque régime
- ✅ JavaScript `exportRegimePdf()` pour gérer l'export

### 6. **CSS** (`public/assets/css/regimes-section.css`)
- ✅ Nouveaux styles pour:
  - `.regime-percentage` - Badge de pourcentage
  - `.composition-bars` - Barres de composition
  - `.detail-group` - Groupes de détails
  - `.btn-export-pdf` - Bouton d'export PDF

### 7. **Vues obsolètes** (`app/Views/regimes/`)
- ⚠️ `details.php` - Marquée obsolète (lance 404)
- ⚠️ `details-print.php` - Marquée obsolète (lance 404)
- ⚠️ `details-pdf.php` - Marquée obsolète (lance 404)
- ✅ `export-pdf.php` - **Nouvelle vue** pour export PDF des régimes

## Impact utilisateur

### Avant
- Voir comment combiner plusieurs régimes
- Naviguer vers une page détails complexe
- Calculer soi-même la durée totale

### Après
- Voir immédiatement tous les régimes disponibles adaptés aux besoins
- Voir le pourcentage du traitement complet pour chacun
- Exporter directement en PDF n'importe quel régime
- Interface plus claire et intuitive

## Notes techniques

### Compatibilité
- Les anciennes méthodes du régimeModel sont conservées pour compatibilité
- Elles sont marquées `@deprecated` dans la documentation
- Si du code externe les utilise, aucune erreur ne survient (juste des warns)

### Performance
- ✅ Une seule requête à la base de données par régime
- ✅ Calcul simple en O(n) où n = nombre de régimes
- ✅ Pas de récursion

### Sécurité
- ✅ Vérification utilisateur connecté
- ✅ Échappement des données avec `esc()`
- ✅ Gestion des 404 pour les URLs invalides

## Testing recommandé

1. ✓ Vérifier que le dashboard affiche les régimes avec pourcentages
2. ✓ Tester l'export PDF pour chacun des régimes
3. ✓ Vérifier l'accès aux anciennes URLs (devrait retourner 404)
4. ✓ Tester avec différents poids et objectifs
5. ✓ Tester avec un utilisateur Gold (réduction sur les prix)
6. ✓ Vérifier le rendu PDF sur différents navigateurs

## Migration future (optionnel)

Si désiré, on peut:
1. Supprimer complètement les anciennes fonctions (non-breaking change en lui offrant une période de transition)
2. Optimiser le CSS avec des variables CSS
3. Ajouter du tri/filtre sur les régimes affichés
4. Ajouter des statistiques d'efficacité estimée
5. Permettre la comparaison de régimes côte à côte

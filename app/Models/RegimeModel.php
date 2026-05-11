<?php 
namespace App\Models;
use CodeIgniter\Model;
use App\Models\ActiviteModel;

/**
 * RegimeModel - Gestion des régimes
 * 
 * CHANGEMENTS (11 mai 2026):
 * - Les anciennes méthodes de calcul de combinaisons (getAllRegimes, getRegimes, etc.) sont conservées 
 *   pour compatibilité mais NE SONT PLUS UTILISÉES
 * - Nouvelle méthode: getRegimesAvecPourcentage() - Retourne tous les régimes avec leur pourcentage calculé
 * - Utilise la règle de trois pour adapter la durée du régime au poids à perdre
 * 
 * NOUVEAUX CALCULS:
 * - Pour chaque régime: jours_calcules = (duree_base × poids_cible) / poids_base
 * - Pourcentage: (jours_calcules / duree_base) × 100
 */
class RegimeModel extends Model
{
    protected $table='regimes';
    protected $primaryKey='id';
    protected $allowedFields=['nom','description',
    'pct_viande','pct_poisson','pct_volaille',
    'variation_poids_kg','duree_jours','prix'];

    /**
     * Calcule le poids optimal à atteindre pour l'utilisateur
     * en fonction de son IMC actuel et de son objectif
     * 
     * @param float $imc_utilisateur L'IMC actuel de l'utilisateur
     * @param float $taille_cm La taille en centimètres
     * @param string $objectif L'objectif de l'utilisateur (augmenter_poids, reduire_poids, imc_ideal)
     * @return float Le poids optimal à atteindre
     */
    public function calculPoidsOptimal(float $imc_utilisateur, float $taille_cm, string $objectif): float
    {
        $categorieIMCModel = new CategorieIMCModel();
        
        // Récupérer les seuils du "Poids normal"
        $poidsNormal = $categorieIMCModel
            ->where('label', 'Poids normal')
            ->first();
        
        if (!$poidsNormal) {
            return 0; // Fallback si la catégorie n'existe pas
        }
        
        $seuilMinNormal = $poidsNormal['seuil_min']; // 18.5
        $seuilMaxNormal = $poidsNormal['seuil_max']; // 25
        
        // Convertir taille en mètres pour le calcul
        $taille_m = $taille_cm / 100;
        
        // Si IMC en dessous de la normale, viser l'IMC minimum normal
        if ($imc_utilisateur < $seuilMinNormal) {
            return round(($seuilMinNormal * $taille_m * $taille_m), 2);
        }
        
        // Si IMC au-dessus de la normale, viser l'IMC maximum normal
        if ($imc_utilisateur > $seuilMaxNormal) {
            return round(($seuilMaxNormal * $taille_m * $taille_m), 2);
        }
        
        // Si IMC dans la normale, appliquer l'objectif
        if ($objectif === 'imc_ideal') {
            // Retourner le poids actuel (IMC idéal = situation actuelle)
            return round(($imc_utilisateur * $taille_m * $taille_m), 2);
        }
        
        if ($objectif === 'reduire_poids') {
            // Viser le seuil minimum de l'IMC normal
            return round(($seuilMinNormal * $taille_m * $taille_m), 2);
        }
        
        if ($objectif === 'augmenter_poids') {
            // Viser le seuil maximum de l'IMC normal
            return round(($seuilMaxNormal * $taille_m * $taille_m), 2);
        }
        
        return 0;
    }

    /**
     * Obtient tous les régimes avec le même effet (variation de poids)
     * que ceux calculés pour atteindre le poids optimal
     * 
     * @param float $variationPoids La variation de poids nécessaire (ex: -8.5 pour perdre 8.5kg)
     * @return array Tableau de régimes avec leur pourcentage de jours calculé
     * 
     * Utilise la règle de trois :
     * Si un régime fait -6kg en 30 jours et qu'on doit perdre 15kg,
     * alors il faut : (30 × 15) / 6 = 75 jours
     * On arrondit à la valeur inférieure : floor(75) = 75 jours
     */
    public function getRegimesAvecPourcentage(float $variationPoids): array
    {
        $model = new ActiviteModel();    

        // Déterminer si on cherche des régimes de perte ou gain de poids
        $isWeightLoss = $variationPoids < 0;

        $regimes = [];
        $activites = [];
        
        if ($isWeightLoss) {
            $regimes = $this->where('variation_poids_kg <', 0)->findAll();
            $activites = $model->where('variation_poids_kg <', 0)->findAll();
        } else {
            $regimes = $this->where('variation_poids_kg >', 0)->findAll();
            $activites = $model->where('variation_poids_kg >', 0)->findAll();
        }
        
        $result = [];
        
        foreach ($activites as $activite) {
            $newPoids = $variationPoids - $activite['variation_poids_kg'];
            $variationPoidsAbs = abs($newPoids);
            foreach ($regimes as $regime) {
                $variationRegime = abs($regime['variation_poids_kg']);
                
                // Appliquer la règle de trois pour calculer les jours
                // jours_calcules = (duree_base × poids_cible) / poids_base
                $joursCalcules = ($regime['duree_jours'] * $variationPoidsAbs) / $variationRegime;
                
                // Arrondir à la valeur inférieure pour avoir un nombre de jours entier
                $joursCalcules = floor($joursCalcules);
                
                // Calculer le pourcentage : (jours calculés / jours base) × 100
                $pourcentage = ($joursCalcules / $regime['duree_jours']) * 100;
                $pourcentage = round($pourcentage, 2);

                $regime['variation_poids_kg'] *= $pourcentage / 100;
                $regime['variation_poids_kg'] = round($regime['variation_poids_kg'], 2);

                $regime['prix'] *= $pourcentage / 100;
                $regime['prix'] = round($regime['prix'] / 100) * 100;
                
                $result[] = [
                    'regime' => $regime,
                    'activite' => $activite,
                    'jours_calcules' => $joursCalcules,
                    'pourcentage' => $pourcentage,
                ];
            }
        }
        
        return $result;
    }
}
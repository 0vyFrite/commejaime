<?php 
namespace App\Models;
use CodeIgniter\Model;
use App\Models\ActiviteModel;
class RegimeModel extends Model
{
    protected $table = 'regimes';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'nom', 'description',
        'pct_viande', 'pct_poisson', 'pct_volaille',
        'variation_poids_kg', 'duree_jours', 'prix'
    ];

    public function calculPoidsOptimal(float $imc, float $taille_cm, string $objectif): float
    {
        $poidsNormal = (new CategorieIMCModel())->where('label', 'Poids normal')->first();

        if (!$poidsNormal) return 0;

        $taille_m  = $taille_cm / 100;
        $seuilMin  = $poidsNormal['seuil_min']; // 18.5
        $seuilMax  = $poidsNormal['seuil_max']; // 25.0

        if ($imc < $seuilMin) return round($seuilMin * $taille_m ** 2, 2);
        if ($imc > $seuilMax) return round($seuilMax * $taille_m ** 2, 2);

        // IMC dans la normale : appliquer l'objectif
        $cible = match ($objectif) {
            'reduire_poids'   => $seuilMin,
            'augmenter_poids' => $seuilMax,
            default           => $imc,   // imc_ideal = rester au même poids
        };

        return round($cible * $taille_m ** 2, 2);
    }

    public function getRegimesAvecPourcentage(float $variationPoids): array
    {
        $activiteModel = new ActiviteModel();
        $perteOuGain   = $variationPoids < 0 ? '<' : '>';

        $regimes  = $this->where('variation_poids_kg ' . $perteOuGain, 0)->findAll();
        $activites = $activiteModel->where('variation_poids_kg ' . $perteOuGain, 0)->findAll();

        $result = [];

        foreach ($activites as $activite) {
            $poidsRestant = abs($variationPoids - $activite['variation_poids_kg']);

            foreach ($regimes as $regime) {
                $variationBase  = abs($regime['variation_poids_kg']);
                $joursCalcules  = (int) floor($regime['duree_jours'] * $poidsRestant / $variationBase);
                $pourcentage    = round($joursCalcules / $regime['duree_jours'] * 100);

                $regimeAjuste = $regime;
                $regimeAjuste['variation_poids_kg'] = round($regime['variation_poids_kg'] * $pourcentage / 100, 2);
                $regimeAjuste['prix']               = round($regime['prix'] * $pourcentage / 100 / 100) * 100;

                $result[] = [
                    'regime'        => $regimeAjuste,
                    'activite'      => $activite,
                    'jours_calcules' => $joursCalcules,
                    'pourcentage'   => $pourcentage,
                ];
            }
        }

        return $result;
    }
}
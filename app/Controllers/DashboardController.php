<?php
namespace App\Controllers;
use App\Models\ProfilSanteModel;
use App\Models\RegimeModel;
use App\Models\CategorieIMCModel;

class DashboardController extends BaseController
{
    public function index()
    {
        $user = session()->get('user');
        $profilModel = new ProfilSanteModel();
        $regimeModel = new RegimeModel();
        $categorieModel = new CategorieIMCModel();
        $profil=$profilModel->getProfilUser($user['id']);
        $categoriesIMC = $categorieModel->getAllCategories();

        $regimesAvecPourcentage = [];
        $poidsOptimal = 0;

        // Vérifier que le profil existe avant de calculer les régimes
        if ($profil) {
            // Calculer le poids optimal
            $poidsOptimal = $regimeModel->calculPoidsOptimal($profil['imc'], $profil['taille_cm'], $profil['objectif']);
            // Variation négative = perdre du poids, Variation positive = gagner du poids
            $variationPoids = $poidsOptimal - $profil['poids_kg'];
            
            // Seulement si la variation de poids n'est pas nulle (tolérance 0.5kg)
            if (abs($variationPoids) > 0.5) {
                // Récupérer tous les régimes avec le même effet que la variation calculée
                $regimesAvecPourcentage = $regimeModel->getRegimesAvecPourcentage($variationPoids);
            }
        }

        return view('dashboard/index', 
        [   'user'=>$user,
            'profil'=>$profil,
            'categoriesIMC'=>$categoriesIMC,
            'regimes' => $regimesAvecPourcentage,
            'poidsOptimal' => $poidsOptimal,
        ]);
    }

}
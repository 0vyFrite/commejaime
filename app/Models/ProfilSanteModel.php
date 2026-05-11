<?php 
namespace App\Models;
use CodeIgniter\Model;

class ProfilSanteModel extends Model
{
    protected $table='profils_sante';
    protected $primaryKey='id';

    protected $allowedFields=['utilisateur_id','taille_cm','poids_kg','imc','objectif'];

    protected $validationRules = [
        'taille_cm'=>'required|decimal|greater_than[50]|less_than[250]',
        'poids_kg'=>'required|decimal|greater_than[10]|less_than[300]',
        'objectif'=>'required|in_list[augmenter_poids,reduire_poids,imc_ideal]',
    ];

    public function calculIMC(float $taille_cm, float $poids)
    {
        $taille_m = $taille_cm / 100;
        return round($poids / ($taille_m * $taille_m), 2);
    }

    public function interpreterIMC(float $imc)
    {
        $categorieModel = new CategorieIMCModel();
        $categorie = $categorieModel->getCategorieByIMC($imc);
        return $categorie ? $categorie['label'] : 'Catégorie inconnue';
    }
    

    public function getProfilUser(int $userId)
    {
        return $this->where('utilisateur_id', $userId)->first();
    }

}

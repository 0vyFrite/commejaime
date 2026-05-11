<?php

namespace App\Models;

use CodeIgniter\Model;

class CategorieIMCModel extends Model
{
    protected $table = 'categories_imc';
    protected $primaryKey = 'id';
    protected $allowedFields = ['seuil_min', 'seuil_max', 'label', 'description'];

    public function getCategorieByIMC(float $imc)
    {
        return $this->where('seuil_min <=', $imc)
                    ->where('seuil_max >', $imc)
                    ->first();
    }

    public function getAllCategories()
    {
        return $this->orderBy('seuil_min', 'ASC')->findAll();
    }
}

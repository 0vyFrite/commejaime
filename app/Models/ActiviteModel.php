<?php
namespace App\Models;
use CodeIgniter\Model;

class ActiviteModel extends Model
{
    protected $table = 'activites';
    protected $primaryKey = 'id';
    protected $allowedFields = ['nom', 'description', 
    'variation_poids_kg', 'frequence_semaine', 'duree_minutes'];
}
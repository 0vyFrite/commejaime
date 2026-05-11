<?php
 namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table = 'utilisateurs';
    protected $primaryKey = 'id_user';

    protected $allowedFields = ['nom', 'email', 'mot_de_passe', 'genre','solde_portefeuille','est_gold',];
    protected $validationRules = [
        'nom'           => 'required|min_length[2]|max_length[100]',
        'email'         => 'required|valid_email|is_unique[utilisateurs.email]',
        'mot_de_passe'  => 'required|min_length[6]',
        'genre'         => 'required|in_list[homme,femme,autre]',
    ];

    public function getUserByEmail($email)
    {
        return $this->where('email', $email)->first();
    }
}

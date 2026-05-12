<?php

namespace App\Controllers;

use App\Models\UserModel;
use App\Models\ProfilSanteModel;
use App\Models\CategorieIMCModel;

class ProfilController extends BaseController
{
    public function index()
    {
        $user = session()->get('user');
        $profilModel = new ProfilSanteModel();
        $categorieModel = new CategorieIMCModel();
        $userModel = new UserModel();

        // Récupérer les données complètes de l'utilisateur (avec solde à jour)
        $userComplet = $userModel->find($user['id']);
        $profil = $profilModel->getProfilUser($user['id']);
        $categoriesIMC = $categorieModel->getAllCategories();

        // Catégorie IMC actuelle
        $imc = $profil['imc'] ?? 0;
        $categorieActuelle = null;
        foreach ($categoriesIMC as $cat) {
            if ($imc >= $cat['seuil_min'] && $imc < $cat['seuil_max']) {
                $categorieActuelle = $cat;
                break;
            }
        }

        return view('profil/profil', [
            'user'            => $user,
            'userComplet'     => $userComplet,
            'profil'          => $profil,
            'categoriesIMC'   => $categoriesIMC,
            'categorieActuelle' => $categorieActuelle,
        ]);
    }

    public function rechargerPortefeuille()
    {
        $user = session()->get('user');
        $code = trim($this->request->getPost('code'));

        if (empty($code)) {
            session()->setFlashdata('erreur', 'Veuillez entrer un code de recharge.');
            return redirect()->back();
        }

        $db = \Config\Database::connect();

        // Chercher le code dans la base
        $codeRecord = $db->table('codes_portefeuille')
            ->where('code', $code)
            ->get()
            ->getRowArray();

        if (!$codeRecord) {
            session()->setFlashdata('erreur', '❌ Code invalide. Vérifiez le code saisi.');
            return redirect()->back();
        }

        if ($codeRecord['utilise']) {
            session()->setFlashdata('erreur', '❌ Ce code a déjà été utilisé.');
            return redirect()->back();
        }

        $montant = $codeRecord['montant'];

        // Démarrer une transaction DB
        $db->transStart();

        // Marquer le code comme utilisé
        $db->table('codes_portefeuille')->where('id', $codeRecord['id'])->update([
            'utilise'          => true,
            'utilise_par'      => $user['id'],
            'date_utilisation' => date('Y-m-d H:i:s'),
        ]);

        // Mettre à jour le solde de l'utilisateur
        $db->table('utilisateurs')
            ->where('id_user', $user['id'])
            ->update([
                'solde_portefeuille' => $db->query(
                    "SELECT solde_portefeuille + {$montant} AS nouveau_solde FROM utilisateurs WHERE id_user = {$user['id']}"
                )->getRowArray()['nouveau_solde']
            ]);

        // Enregistrer la transaction
        $db->table('transactions')->insert([
            'utilisateur_id'   => $user['id'],
            'code_id'          => $codeRecord['id'],
            'montant'          => $montant,
            'type_transaction' => 'recharge_code',
        ]);

        $db->transComplete();

        if ($db->transStatus() === false) {
            session()->setFlashdata('erreur', '❌ Une erreur est survenue. Réessayez.');
            return redirect()->back();
        }

        // Mettre à jour la session avec le nouveau solde
        $userModel = new UserModel();
        $userMaj = $userModel->find($user['id']);
        $userSession = session()->get('user');
        $userSession['solde_portefeuille'] = $userMaj['solde_portefeuille'];
        session()->set('user', $userSession);

        $montantFormate = number_format($montant, 0, ',', ' ');
        session()->setFlashdata('succes', "✅ Portefeuille rechargé avec succès ! +{$montantFormate} Ar ajoutés.");
        return redirect()->back();
    }

    public function acheterGold()
    {
        $user = session()->get('user');

        if ($user['est_gold'] ?? false) {
            session()->setFlashdata('erreur', 'Vous êtes déjà membre Gold.');
            return redirect()->to('/dashboard');
        }

        $prixGold = 50000;
        $db = \Config\Database::connect();

        // Vérifier le solde
        $userModel = new UserModel();
        $userComplet = $userModel->find($user['id']);

        if ($userComplet['solde_portefeuille'] < $prixGold) {
            $manque = $prixGold - $userComplet['solde_portefeuille'];
            $manqueFormate = number_format($manque, 0, ',', ' ');
            session()->setFlashdata('erreur', "❌ Solde insuffisant. Il vous manque {$manqueFormate} Ar. Rechargez votre portefeuille.");
            return redirect()->to('/dashboard');
        }

        $db->transStart();

        // Déduire le solde
        $nouveauSolde = $userComplet['solde_portefeuille'] - $prixGold;
        $db->table('utilisateurs')->where('id_user', $user['id'])->update([
            'solde_portefeuille' => $nouveauSolde,
            'est_gold'           => true,
        ]);

        // Enregistrer dans options_gold
        $db->table('options_gold')->insert([
            'utilisateur_id' => $user['id'],
            'prix_paye'      => $prixGold,
            'remise_pct'     => 15.00,
        ]);

        // Transaction
        $db->table('transactions')->insert([
            'utilisateur_id'   => $user['id'],
            'montant'          => $prixGold,
            'type_transaction' => 'achat_gold',
        ]);

        $db->transComplete();

        if ($db->transStatus() === false) {
            session()->setFlashdata('erreur', '❌ Une erreur est survenue. Réessayez.');
            return redirect()->to('/dashboard');
        }

        // Mettre à jour la session
        $userSession = session()->get('user');
        $userSession['est_gold'] = true;
        $userSession['solde_portefeuille'] = $nouveauSolde;
        session()->set('user', $userSession);

        session()->setFlashdata('succes', '⭐ Félicitations ! Vous êtes maintenant membre Gold avec 15% de remise sur tous les régimes !');
        return redirect()->to('/dashboard');
    }
}
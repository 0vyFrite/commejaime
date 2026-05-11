<?php

namespace App\Controllers;

use App\Models\UserModel;
use App\Models\ProfilSanteModel;
use App\Models\CategorieIMCModel;

class AuthController extends BaseController
{
    // inscription part1
    public function inscriptionEtape1()
    {
        return view('auth/inscription_etape1');
    }

    public function inscriptionEtape1Post()
    {
        if (!$this->validate([
            'nom'=>'required|min_length[2]',
            'email'=>'required|valid_email|is_unique[utilisateurs.email]',
            'genre'=>'required|in_list[homme,femme,autre]',
            'mot_de_passe'=>'required|min_length[6]',
            'confirmation'=>'required|matches[mot_de_passe]',
        ])) {
            return view('auth/inscription_etape1', [
                'erreurs'=>$this->validator->getErrors(),
                'anciennes'=>$this->request->getPost(),
            ]);
        }

        session()->set('inscription_etape1', [
            'nom'=> $this->request->getPost('nom'),
            'email'=> $this->request->getPost('email'),
            'genre'=> $this->request->getPost('genre'),
            'mot_de_passe'=> $this->request->getPost('mot_de_passe'),
        ]);

        return redirect()->to('/inscription/etape2');
    }

    // inscription part2
    public function inscriptionEtape2()
    {
        if (!session()->get('inscription_etape1')) {
            return redirect()->to('/inscription');
        }
        $categorieModel = new CategorieIMCModel();
        $categoriesIMC = $categorieModel->getAllCategories();
        return view('auth/inscription_etape2', [
            'categoriesIMC' => $categoriesIMC,
        ]);
    }

    public function inscriptionEtape2Post()
    {
        $etape1=session()->get('inscription_etape1');
        if (!$etape1) {
            return redirect()->to('/inscription');
        }

        if (!$this->validate([
            'taille_cm'=>'required|decimal|greater_than[50]|less_than[250]',
            'poids_kg' =>'required|decimal|greater_than[10]|less_than[300]',
            'objectif' =>'required|in_list[augmenter_poids,reduire_poids,imc_ideal]',
        ])) {
            $categorieModel = new CategorieIMCModel();
            $categoriesIMC = $categorieModel->getAllCategories();
            return view('auth/inscription_etape2', [
                'erreurs'=>$this->validator->getErrors(),
                'anciennes'=>$this->request->getPost(),
                'categoriesIMC' => $categoriesIMC,
            ]);
        }

        $userModel = new UserModel();
        $profilModel = new ProfilSanteModel();
        $taille = (float) $this->request->getPost('taille_cm');
        $poids = (float) $this->request->getPost('poids_kg');
        $imc = $profilModel->calculIMC($taille, $poids);

        $userId=$userModel->insert([
            'nom'=>$etape1['nom'],
            'email'=>$etape1['email'],
            'genre'=>$etape1['genre'],
            'mot_de_passe'=>$etape1['mot_de_passe'],
        ]);

        $profilModel->insert([
            'utilisateur_id'=>$userId,
            'taille_cm'=>$taille,
            'poids_kg'=>$poids,
            'imc'=>$imc,
            'objectif'=>$this->request->getPost('objectif'),
        ]);
        session()->remove('inscription_etape1');
        $user = $userModel->find($userId);
        session()->set('user', [
            'id'=>$user['id_user'],
            'nom'=>$user['nom'],
            'email'=>$user['email'],
            'est_gold'=> $user['est_gold'],
            'role'=>'user',
        ]);

        return view('auth/inscription_succes', [
            'nom'=>$user['nom'],
            'imc'=>$imc,
            'imcTexte'=>$profilModel->interpreterIMC($imc),
        ]);
    }

    // LOGIN
    public function loginForm()
    {
        if (session()->get('user')) {
            return redirect()->to('/dashboard');
        }
        return view('auth/login');
    }

    public function login()
    {
        if (!$this->validate([
            'email'=>'required|valid_email',
            'mot_de_passe'=>'required',
        ])) {
            return view('auth/login', [
                'erreurs'=>$this->validator->getErrors(),
                'anciennes'=>$this->request->getPost(),
            ]);
        }

        $userModel=new UserModel();
        $user=$userModel->getUserByEmail($this->request->getPost('email'));

        if (!$user||$user['mot_de_passe'] !==$this->request->getPost('mot_de_passe')) {
            return view('auth/login',[
                'erreurs' => ['global' => 'Email ou mot de passe incorrect.'],
            ]);
        }

        session()->set('user', [
            'id'=> $user['id_user'],
            'nom'=> $user['nom'],
            'email'=> $user['email'],
            'est_gold'=> $user['est_gold'],
            'role'=>'user',
        ]);
        return redirect()->to('/dashboard');
    }

    public function logout()
    {
        session()->destroy();
        return redirect()->to('/login');
    }
}
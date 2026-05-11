<?php

namespace App\Controllers;

use App\Models\RegimeModel;
use App\Models\ProfilSanteModel;
use App\Models\ActiviteModel;

class RegimesController extends BaseController
{
    /**
     * Exporte un régime individuel en PDF
     * 
     * @param int $regimeId L'ID du régime
     * @param int $activiteId L'ID de l'activité associée
     * @param int $pourcentage Le pourcentage du traitement complet
     */
    public function exportRegimePdf($regimeId, $activiteId, $pourcentage)
    {
        $user = session()->get('user');
        if (!$user) {
            return redirect()->to('/login');
        }

        $regimeModel = new RegimeModel();
        $profilModel = new ProfilSanteModel();
        $activiteModel = new ActiviteModel();

        $regime = $regimeModel->find($regimeId);
        if (!$regime) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $activite = $activiteModel->find($activiteId);
        if (!$activite) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $profil = $profilModel->getProfilUser($user['id']);

        $totalPoids = $regime['variation_poids_kg'];
        $totalPoids *= $pourcentage / 100;
        $totalPoids = round($totalPoids, 2);
        
        $totalDuree = $regime['duree_jours'];
        $totalDuree *= $pourcentage / 100;
        $totalDuree = floor($totalDuree);
        
        $totalPrix = $regime['prix'];
        $totalPrix *= $pourcentage / 100;
        $totalPrix = round($totalPrix / 100) * 100;

        return view('regimes/export-pdf', [
            'user' => $user,
            'profil' => $profil,
            'regime' => $regime,
            'activite' => $activite,
            'pourcentage' => $pourcentage,
            'totalPoids' => $totalPoids,
            'totalDuree' => $totalDuree,
            'totalPrix' => $totalPrix,
        ]);
    }
}
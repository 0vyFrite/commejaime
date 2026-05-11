<?php

namespace App\Controllers;

use App\Models\RegimeModel;
use App\Models\ProfilSanteModel;

class RegimesController extends BaseController
{
    /**
     * Exporte un régime individuel en PDF
     * 
     * @param int $regimeId L'ID du régime
     * @param float $pourcentage Le pourcentage du traitement complet
     */
    public function exportRegimePdf($regimeId, $pourcentage)
    {
        $user = session()->get('user');
        if (!$user) {
            return redirect()->to('/login');
        }

        $regimeModel = new RegimeModel();
        $profilModel = new ProfilSanteModel();

        $regime = $regimeModel->find($regimeId);
        if (!$regime) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $profil = $profilModel->getProfilUser($user['id']);

        // Calculer les jours nécessaires (pourcentage du régime de base)
        $joursNecessaires = floor(($pourcentage / 100) * $regime['duree_jours']);

        $html = view('regimes/export-pdf', [
            'user' => $user,
            'profil' => $profil,
            'regime' => $regime,
            'pourcentage' => $pourcentage,
            'joursNecessaires' => $joursNecessaires,
        ], ['saveData' => false]);

        // Utiliser Dompdf pour générer le PDF
        $options = new \Dompdf\Options();
        $options->set('isHtml5ParserEnabled', true);
        $options->set('defaultFont', 'Courier');
        
        $dompdf = new \Dompdf\Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        return $dompdf->stream('regime-' . $regime['id'] . '.pdf', ['Attachment' => 0]);
    }
}
<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;

/**
 * Class SpecializationController
 *
 * Contrôleur gérant la consultation, la création, la modification et
 * la suppression des spécialisations (classes) de héros.
 */
class SpecializationController extends BaseController
{
    /**
     * Affiche la liste complète des spécialisations (Page d'index/roster).
     *
     * @return string|ResponseInterface Rend la vue de la liste ou renvoie une réponse HTTP.
     */
    public function index()
    {
        // TODO: Récupérer les données via SpecializationModel et charger la vue correspondante
    }
}
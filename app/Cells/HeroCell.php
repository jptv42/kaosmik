<?php

namespace App\Cells;

use App\Entities\Hero;
use CodeIgniter\View\Cells\Cell;

/**
 * Class HeroCell
 *
 * Composant View Cell CodeIgniter 4 permettant d'afficher les données d'un héros.
 */
class HeroCell extends Cell
{
    /**
     * L'entité Hero ou le tableau contenant les données du personnage à afficher.
     *
     * @var Hero|array|null
     */
    public $character;

    /**
     * Le contexte d'affichage du composant (ex: 'roster', 'card', 'profile').
     * Permet d'adapter le rendu HTML/CSS selon le composant parent.
     *
     * @var string
     */
    public string $context = 'roster';
}
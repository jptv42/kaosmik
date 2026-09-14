<?php

namespace App\Models;

use App\Entities\RarityLevel;
use CodeIgniter\Model;

class RarityLevelModel extends Model
{
    protected $table            = 'rarity_levels';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = RarityLevel::class;
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['name', 'color', 'power_multiplier', 'cost_multiplier', 'appearance_rate'];
    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    protected array $casts = [];
    protected array $castHandlers = [];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    // Validation
    protected $validationRules      = [];
    protected $validationMessages   = [];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert   = ['convertAppearanceRate'];
    protected $afterInsert    = ['calculateCommonAppearanceRate'];
    protected $beforeUpdate   = ['convertAppearanceRate'];
    protected $afterUpdate    = ['calculateCommonAppearanceRate'];
    protected $beforeFind     = [];
    protected $afterFind      = [];
    protected $beforeDelete   = ['protectDefaultRarity'];
    protected $afterDelete    = ['calculateCommonAppearanceRate'];

    protected function convertAppearanceRate(array $data) {
        if (isset($data['data']['appearance_rate'])) {
            $data['data']['appearance_rate'] = (float) str_replace(',', '.', $data['data']['appearance_rate']);
        }
        return $data;
    }

    /**
     * Empêche la suppression/modification de la valeur par défaut Commun (id 1)
     * @throws \Exception
     */
    protected function protectDefaultRarity(array $data) {

        $id = $data['id'][0] ?? null;
        if($id == 1) {
            throw new \Exception('Interdiction de modifier ou supprimer la rareté par défaut (commun)');
        }
        return $data;
    }

    protected function calculateCommonAppearanceRate(array $data) {
        //Gestion de l'ID
        $id = $data['id'];
        if(is_array($id)) {
            $id = $id[0] ?? null;
        }
        //On bloque quand même toujours le 1
        if ($id == 1) {
            return $data;
        }

        //On calcul la somme de toutes les raretés autres que commun
        $result = $this->select('SUM(appearance_rate) as total')
            ->where(['id !=' => 1])
            ->first();

        $sum = $result->total ?? 0;
        $newCommonRate = 100 - $sum;
        //On empêche le negatif
        $newCommonRate = max(0, $newCommonRate);

        //On MaJ le commun en empêchant le callback d'être appeler pour ne pas boucler à l'infini
        $this->db->table('rarity_levels')
            ->update(['appearance_rate' => $newCommonRate], ['id' => 1]);

        return $data;
    }

     /**
     * Sélectionne une rareté de façon aléatoire en fonction de son taux d'apparition (tirage pondéré).
     *
     * @return object|array|null La rareté tirée au sort
     */
    public function getRandomRarity()
    {
        // 1. Générer un nombre aléatoire entre 1 et 100 (pourcentage)
        $random = rand(1, 100);
        $sum = 0;

        // 2. Récupérer toutes les raretés triées par taux d'apparition décroissant
        // Trier par taux décroissant permet d'optimiser les performances de la boucle
        $rarities = $this->orderBy('appearance_rate', 'DESC')->findAll();

        // 3. Parcourir les raretés et cumuler les probabilités (roulette d'itération)
        foreach ($rarities as $rarity) {
            $sum += $rarity->appearance_rate;

            // Si le nombre aléatoire tombe dans la tranche cumulée actuelle, c'est cette rareté qui est choisie
            if ($random <= $sum) {
                return $rarity;
            }
        }
    }
}
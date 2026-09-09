<?php

namespace App\Entities;

use App\Models\SpecializationModel;
use CodeIgniter\Entity\Entity;

class HeroModel extends Entity
{
    protected $datamap = [];
    protected $dates   = ['created_at', 'updated_at', 'deleted_at'];
    protected $casts   = [
                            'specialization_id' => 'integer',
                            'name'=>'string',
                            'description'=>'string',
                            'power_min'=>'integer',
                            'power_max'=>'integer',
                            'cost_credits_min'=>'integer',
                            'cost_credits_max'=>'integer',
                            'level-required'=>'integer',
];
    protected $specialization = null;

    public function getSpecialization(){
        // Si la spécialisation n'a PAS encore été chargée ET qu'il existe un ID valide...
        if($this->specialization === null && ($this->specialization_id)){
            // 1. Instanciation / Récupération du modèle
            $sm = model(SpecializationModel::class);

            // 2. Requête en BDD et stockage du résultat dans la propriété de la classe
            $this->specialization = $sm->where('id', $this->specialization_id)->first();
        }

        // Renvoie la spécialisation (qu'elle vienne d'être chargée ou qu'elle l'ait été plus tôt)
        return $this->specialization;
    }
}

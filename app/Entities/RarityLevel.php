<?php

namespace App\Entities;

use CodeIgniter\Entity\Entity;
use App\Models\RarityLevelModel;

class RarityLevel extends Entity
{
    protected $attributes = [
        'id'  => null,
        'name' => null,
        'color' => '#ffffff',
        'power_multiplier' => 1.00,
        'cost_multiplier' => 1.00,
        'appearance_rate' => 1,
    ];
    protected $casts   = [
        'id'  => 'integer',
        'name' => 'string',
        'color' => 'string',
        'power_multiplier' => 'float',
        'cost_multiplier' => 'float',
        'appearance_rate' => 'float',
    ];
    protected $datamap = [];
    protected $dates   = ['created_at', 'updated_at'];

}

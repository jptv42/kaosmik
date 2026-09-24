<?php

namespace App\Entities;

use App\Models\HeroModel;
use App\Models\MissionResolutionModel;
use CodeIgniter\Entity\Entity;

class MissionHero extends Entity
{
    protected $attributes = [
        'id' => null,
        'mission_resolution_id' => null,
        'hero_id' => null,
        'stamina_consumed' => null,
    ];
    protected $datamap = [];
    protected $dates   = ['created_at', 'updated_at'];
    protected $casts   = [
        'id' => 'int',
        'mission_resolution_id' => 'int',
        'hero_id' => 'int',
        'stamina_consumed' => 'int',
    ];

    protected ?MissionResolution $missionResolution = null;
    protected ?Hero $hero = null;

    public function getMissionResolution(): ?MissionResolution {
        if($this->missionResolution === null && ($this->attributes['mission_resolution_id'])) {
            $missionResolutionModel = model(MissionResolutionModel::class);
            $this->missionResolution = $missionResolutionModel->find($this->attributes['mission_resolution_id']);
        }
        return $this->missionResolution;
    }

    public function getHero(): ?Hero {
        if($this->hero === null && ($this->attributes['hero_id'])) {
            $heroModel = model(HeroModel::class);
            $this->hero = $heroModel->find($this->attributes['hero_id']);
        }
        return $this->hero;
    }
}
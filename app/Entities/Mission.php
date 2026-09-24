<?php

namespace App\Entities;

use CodeIgniter\Entity\Entity;

class Mission extends Entity
{
    protected $attributes = [
        'id' => null,
        'title' => null,
        'description' => null,
        'level_required' => null,
        'power_required_min' => null,
        'power_required_max' => null,
        'stamina_cost_min' => null,
        'stamina_cost_max' => null,
        'team_size_max' => null,
        'credits_reward_min' => null,
        'credits_reward_max' => null,
        'energy_reward_min' => null,
        'energy_reward_max' => null,
        'experience_reward_min' => null,
        'experience_reward_max' => null,
    ];
    protected $datamap = [];
    protected $dates   = ['created_at', 'updated_at', 'deleted_at'];
    protected $casts   = [
        'id' => 'int',
        'title' => 'string',
        'description' => 'string',
        'level_required' => 'int',
        'power_required_min' => 'int',
        'power_required_max' => 'int',
        'stamina_cost_min' => 'int',
        'stamina_cost_max' => 'int',
        'team_size_max' => 'int',
        'credits_reward_min' => 'int',
        'credits_reward_max' => 'int',
        'energy_reward_min' => 'int',
        'energy_reward_max' => 'int',
        'experience_reward_min' => 'int',
        'experience_reward_max' => 'int',
    ];
}
<?php

namespace App\Models;

use App\Entities\LevelThreshold;
use CodeIgniter\Model;

class LevelThresholdModel extends Model
{
    protected $table            = 'level_thresholds';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'level',
        'experience_required'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

}

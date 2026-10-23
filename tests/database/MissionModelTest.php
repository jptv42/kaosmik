<?php

namespace Tests\Database;

use App\Models\HeroModelModel;
use App\Models\MissionModel;
use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\DatabaseTestTrait;

final class MissionModelTest extends CIUnitTestCase
{
    use DatabaseTestTrait;

    protected $migrate   = true;
    protected $refresh   = true;
    protected $DBGroup   = 'tests';
    protected $namespace = null;
    protected $seed      = 'Tests\Support\Database\Seeds\KaosmikTestSeeder';

    //Les missions du seeder
    public function testSeederCreatesTenMissions()
    {
        $this->assertCount(10, (new MissionModel())->findAll());
    }

    //Supprimer (soft delete) une mission p.45

}

<?php

namespace Tests\Database\Services;

use App\Models\HeroModel;
use App\Models\PlayerModel;
use App\Services\MissionService;
use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\DatabaseTestTrait;
use Tests\Support\Traits\CreatesPlayers;

final class MissionServiceTest extends CIUnitTestCase
{
    use DatabaseTestTrait;
    use CreatesPlayers;

    protected $migrate   = true;
    protected $refresh   = true;
    protected $DBGroup   = 'tests';
    protected $namespace = null;
    protected $seed      = 'Tests\Support\Database\Seeds\KaosmikTestSeeder';

    private MissionService $service;

    protected function setUp(): void
    {
        parent::setUp();

        $this->service = new MissionService();
    }

    //Une mission inconnue
    public function testUnknownMissionThrowsException(){
        $player = $this->createPlayer('joueur1');

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Mission introuvable');

        $this->service->processMission($player,9999,[1]);
    }

}
